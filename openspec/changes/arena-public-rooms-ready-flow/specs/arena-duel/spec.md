## MODIFIED Requirements

### Requirement: Sala privada de Duelo
O sistema SHALL criar sala autenticada com visibilidade `PRIVATE` ou `PUBLIC`, configurações permitidas e participantes que escolhem exatamente os assuntos exigidos. `PRIVATE` SHALL exigir código curto para entrada e permanecer visível somente a participantes, exceto na lista do próprio criador. `PUBLIC` SHALL permitir entrada autenticada pelo identificador da sala sem exigir código e SHALL permanecer visível na descoberta enquanto estiver `WAITING`.

#### Scenario: Entrada válida em sala privada
- **WHEN** usuário entra por código disponível de sala `PRIVATE`
- **THEN** o sistema o registra como participante aguardando escolhas

#### Scenario: Entrada válida em sala pública
- **WHEN** usuário autenticado entra em sala `PUBLIC` disponível pelo seu identificador
- **THEN** o sistema o registra como participante aguardando escolhas sem exigir código

### Requirement: Questões e resolução autoritativas
O sistema SHALL iniciar apenas quando todos os participantes tiverem confirmado exatamente os assuntos exigidos, congelar sequência PUBLISHED equilibrada e aceitar uma resposta única antes do prazo do servidor. A Arena SHALL expor aos participantes os assuntos canônicos disponíveis e a confirmação explícita de prontidão antes do início.

#### Scenario: Participante confirma assuntos
- **WHEN** o participante seleciona exatamente a quantidade exigida e confirma
- **THEN** o sistema grava sua prontidão e o criador pode iniciar quando todos estiverem prontos

#### Scenario: Resposta duplicada
- **WHEN** o participante envia outra alternativa para a mesma questão
- **THEN** somente a primeira resposta é persistida

### Requirement: Pontuação e resultado
O sistema SHALL encerrar quando todos respondem ou expira o prazo, pontuar corretas por ordem 100/75/50/25, erradas em -25 e persistir classificação final.

#### Scenario: Correções ordenadas
- **WHEN** quatro respostas corretas são recebidas
- **THEN** o placar concede 100, 75, 50 e 25 em ordem determinística
