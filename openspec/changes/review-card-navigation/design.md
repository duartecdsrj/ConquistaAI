## Context

A fila contínua persiste somente os cards já servidos e devolve o último como card atual. Não existe posição persistida; portanto, o cliente não pode retornar a um card anterior e o servidor não pode servir o próximo antes de uma classificação.

## Goals / Non-Goals

**Goals:**

- Permitir voltar a cards já servidos e avançar sem classificar.
- Preservar seleção dinâmica, isolamento por usuário e eventos de revisão imutáveis.
- Expor à interface somente o estado necessário para habilitar os controles.

**Non-Goals:**

- Pré-selecionar toda a fila, alterar o algoritmo de repetição ou criar classificação automática.
- Permitir navegar para cards que não foram servidos pela sessão.

## Decisions

### Posição atual persistida por sessão

`review_sessions` receberá a posição atual. O próximo card acrescentado mantém a ordem auditável existente; voltar e avançar pelos cards já servidos apenas muda a posição. Sessões legadas sem posição usam o último card servido, preservando o comportamento atual.

Alternativa: guardar a posição apenas no navegador. Foi descartada porque novas abas e recargas perderiam o estado e não permitiriam ao servidor decidir se deve servir novo card.

### Navegação é uma operação explícita e idempotente

Um endpoint autenticado recebe `NEXT` ou `PREVIOUS` e retorna a projeção da sessão. `PREVIOUS` nunca cria card; `NEXT` primeiro reutiliza o próximo card já servido e somente então seleciona um novo candidato, excluindo todos os cards já servidos. Sem próximo candidato, retorna o card atual e informa que não há avanço.

Alternativa: fazer o cliente abrir outra sessão. Foi descartada porque separaria a navegação da auditoria e permitiria repetir cards na mesma sessão.

### Estado de classificação no card atual

A projeção do card atual inclui se ele já foi classificado na sessão. A interface não oferece nova classificação para esse card e mantém a navegação disponível. Isso preserva a unicidade dos eventos sem transformar um reenvio em atualização de histórico.

## Risks / Trade-offs

- [Duas abas avançam ao mesmo tempo] → a operação ocorre em transação; o segundo retorno lê a posição confirmada.
- [Sessão chega ao limite com cards não classificados] → ela permanece navegável até que esses cards sejam classificados; não cria novos cards além do limite.
- [Migração em sessões existentes] → posição nula/zero é interpretada como último card servido e gravada na primeira atualização.

## Migration Plan

1. Adicionar a posição atual com valor padrão compatível e atualizar a projeção Doctrine.
2. Publicar endpoint, DTOs, casos de uso e testes antes da SPA.
3. Atualizar contratos frontend, composable e tela Quasar.
4. Em rollback, a coluna e os vínculos de cards permanecem; a API anterior ignora a posição.

## Open Questions

- Nenhuma.
