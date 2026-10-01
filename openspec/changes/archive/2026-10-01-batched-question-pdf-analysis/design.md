## Context

O worker resiliente já separa candidatos em checkpoints, mas o adaptador Codex executa um contêiner por candidato. A unidade de persistência continuará sendo o candidato; somente a chamada externa passa a ser agrupada.

## Goals / Non-Goals

**Goals:**

- Reduzir inicializações do executor usando lotes limitados por quantidade e caracteres.
- Manter validação, retry, escrita, imagem e telemetria por candidato.
- Permitir que um item inválido de uma resposta em lote não bloqueie os demais.

**Non-Goals:**

- Não alterar contratos HTTP, publicação editorial, taxonomia nem inferir gabaritos.
- Não misturar candidatos de jobs ou usuários distintos no mesmo lote.

## Decisions

- O worker reivindica um checkpoint e busca checkpoints contíguos do mesmo job até o limite configurado. Isso preserva paralelismo entre jobs e reduz chamadas externas.
- Um novo contrato interno `analyzeBatch` recebe e devolve resultados indexados pelo fingerprint. O adaptador unitário permanece como compatibilidade para outros providers futuros.
- A resposta Codex é um objeto com lista de itens; cada item é validado contra seu candidato antes de persistir. Ausência, fingerprint duplicado ou inválido falha somente o item.
- O orçamento usa máximo de candidatos e caracteres estimados, configurados por ambiente. Imagens continuam copiadas com nomes prefixados pelo fingerprint, evitando colisão.
- Telemetria de uma execução é repartida proporcionalmente pelo tamanho dos candidatos e recomposta a partir das análises persistidas no resumo do job.

## Risks / Trade-offs

- [Lote muito grande excede contexto ou timeout] → limite duplo, timeout existente e retry por checkpoint.
- [Resposta parcial] → itens ausentes passam por retry isolado; itens válidos são gravados imediatamente.
- [Tokenização não disponível no runner] → disponibilidade permanece `UNAVAILABLE`, sem estimar custo.

## Migration Plan

1. Implantar o contrato batch e manter `analyze` como adaptador de item único.
2. Configurar limites conservadores no Compose.
3. Reenfileirar somente checkpoints pendentes; rollback reduz o tamanho do lote para 1.

## Open Questions

- Nenhuma.
