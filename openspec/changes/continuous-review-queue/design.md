## Context

A sessão de revisão persiste uma lista de até 100 cards em `review_session_cards` antes de o primeiro card ser respondido. A tela indexa essa lista localmente. Esse modelo impede que a seleção considere a classificação recém-registrada e não representa uma fila sem cards vencidos com continuidade opcional.

## Goals / Non-Goals

**Goals:**

- Entregar um card por vez e selecionar o seguinte após cada classificação.
- Expor a quantidade de cards vencidos, o estado vazio e a elegibilidade de avanço.
- Manter o histórico de classificações imutável, idempotente e limitado ao usuário autenticado.
- Permitir avanço explícito somente quando a fila vencida estiver vazia.

**Non-Goals:**

- Alterar o algoritmo de repetição espaçada ou reclassificar o domínio existente.
- Criar uma sessão compartilhada, offline ou de múltiplos dispositivos.
- Permitir que o cliente escolha arbitrariamente um card futuro.

## Decisions

### Sessão representa contexto, não seleção congelada

Uma sessão ativa continua fornecendo o identificador para idempotência e auditoria, mas não persiste antecipadamente a lista inteira. O serviço escolhe somente o card atual e, após a classificação, busca o próximo com o estado de progresso já atualizado. Isso evita reuso de uma composição obsoleta e preserva `FlashcardReview` como histórico imutável.

Alternativa: substituir totalmente sessões por requisições sem estado. Foi descartada porque removeria o limite natural de reenvio idempotente e o vínculo de auditoria já usado pelas classificações.

### Modos explícitos de fila

`DAILY` seleciona exclusivamente cards com `dueAt <= now`; `ADVANCE` seleciona somente cards futuros, ordenados pela prioridade aplicável e proximidade do vencimento. O endpoint diário informa `canAdvance` apenas quando não houver vencidos e existir candidato futuro. A pessoa inicia o avanço por ação explícita, sem misturar cards antecipados à fila vencida.

Alternativa: fazer a fila diária cair automaticamente em cards futuros. Foi descartada para não surpreender a pessoa nem fazer parecer que revisões não vencidas são obrigatórias.

### Resposta orientada ao próximo estado

O retorno da classificação contém o resultado da repetição e um `queue` com `currentCard` anulável, `overdueCards` e `canAdvance`. A sessão inicial usa a mesma projeção. Assim, a SPA troca o card diretamente pelo estado confirmado da API e não calcula próxima posição, saldo ou elegibilidade localmente.

### Persistência mínima de card servido

O card atual é associado à sessão antes de ser entregue e a classificação valida essa associação. A seleção do próximo exclui cards já classificados na sessão; ao encerrar a fila, a sessão é concluída. Não há migração: sessões ativas legadas podem continuar com sua seleção já persistida e novas sessões usam a seleção incremental.

## Risks / Trade-offs

- [Duas abas pedem o próximo card ao mesmo tempo] → transação e unicidade de card por sessão; a API devolve o estado atual confirmado.
- [Card antecipado fica vencido durante a sessão] → a consulta prioriza vencidos em cada seleção seguinte, mantendo a agenda atual.
- [Sessão legada possui vários vínculos] → o leitor respeita os vínculos existentes e a migração não remove histórico.

## Migration Plan

1. Publicar o contrato de fila e a seleção incremental compatível com registros existentes.
2. Implantar backend e testes antes da SPA.
3. Implantar cliente que consome somente `currentCard` e `queue` retornados.
4. Em rollback, a API mantém dados e eventos existentes; a tela pode retornar ao contrato anterior apenas junto do backend anterior.

## Open Questions

- Nenhuma. O avanço será um modo explícito de sessão e não automático.
