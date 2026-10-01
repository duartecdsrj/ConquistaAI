## Context

O worker mantém um único lock por job e percorre todos os candidatos no mesmo processo. O repositório redefine `startedAt` ao reivindicar um retry e o serviço o redefine em cada checkpoint. O analisador Codex cria um executor efêmero por candidato; indisponibilidade de executor e payload inválido são atualmente indistintos. A escrita detecta duplicata pelo enunciado, mas apenas a ignora.

## Goals / Non-Goals

**Goals:**

- Manter exatamente um início efetivo e um término terminal por job.
- Isolar a falha de um candidato inválido e retentar apenas falhas transitórias de infraestrutura.
- Permitir concorrência configurável e segura por candidato, com reivindicação atômica e reprocessamento idempotente.
- Atualizar uma duplicata apenas quando a nova evidência declara gabarito `OFFICIAL` e a versão armazenada não é oficial.

**Non-Goals:**

- Não inferir, publicar ou substituir gabaritos com estimativas de IA.
- Não alterar o contrato de criação de questões nem executar alterações editoriais fora do importador.

## Decisions

- Criar checkpoints de candidato persistidos e reivindicados com lock. Isso permite múltiplos consumidores do mesmo job sem disputar o contador global; o lock por job atual só permite paralelismo entre PDFs.
- Materializar o candidato antes do analisador e guardar fingerprint, posição, estado, tentativas e erro seguro. Um candidato inválido termina como falho e não agenda retry do job; falhas de executor retornam-no a pendente com backoff limitado.
- Recalcular o resumo do job a partir dos checkpoints terminais. O job termina somente quando não houver candidato pendente/processando e usa `startedAt ?? now`, preservando a primeira reivindicação.
- Fazer a reconciliação no writer Doctrine, após localizar a duplicata, comparando fonte e alternativa pelo rótulo. `OFFICIAL` só substitui `NULL` ou `AI_ESTIMATED`; nenhuma outra alteração acontece.
- Configurar a quantidade de consumidores em Compose por variável e executar cada consumidor em modo contínuo. A concorrência padrão será conservadora para respeitar capacidade e limite do Codex.

## Risks / Trade-offs

- [Concorrência pode exceder limites do provider] → limite configurável, backoff exponencial com jitter e telemetria por candidato.
- [Migração de jobs em andamento] → jobs ativos serão cancelados antes do deploy; registros concluídos permanecem imutáveis.
- [Duplicata com alternativas estruturalmente distintas] → a promoção exige correspondência de rótulo e não troca enunciado, alternativas ou taxonomia.

## Migration Plan

1. Aplicar migration de checkpoints por candidato e índices de lock.
2. Implantar worker com concorrência configurável e manter valor 1 como rollback operacional.
3. Cancelar jobs ativos antes da limpeza autorizada e remover questões/histórico após a validação.
4. Para rollback, reduzir concorrência a 1 e manter checkpoints/retries persistidos; não há perda de job.

## Open Questions

- Nenhuma: a concorrência inicial será limitada por configuração operacional.
