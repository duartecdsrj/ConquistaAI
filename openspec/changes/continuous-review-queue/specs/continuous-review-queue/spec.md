## ADDED Requirements

### Requirement: Fila de revisão incremental
O sistema SHALL entregar no máximo um card atual por sessão de revisão e SHALL selecionar o próximo card somente após registrar a classificação do card atual com sucesso. A seleção diária SHALL conter apenas cards vencidos e respeitar o usuário autenticado.

#### Scenario: Início com cards vencidos
- **WHEN** a pessoa inicia uma revisão diária com cards vencidos
- **THEN** a API retorna um único card atual e a quantidade total de cards vencidos

#### Scenario: Classificação retorna próximo card
- **WHEN** a pessoa classifica o card atual de uma sessão diária que ainda possui cards vencidos elegíveis
- **THEN** a API registra uma única revisão imutável e retorna o próximo card atual selecionado após a atualização do progresso

### Requirement: Estado observável da fila
O sistema SHALL retornar em toda leitura ou atualização da sessão a quantidade de cards vencidos, o card atual anulável e a elegibilidade para revisão antecipada. A API SHALL informar elegibilidade de avanço somente quando não houver cards vencidos e existir ao menos um card futuro elegível para o usuário.

#### Scenario: Fila vencida vazia com avanço disponível
- **WHEN** a pessoa não possui cards vencidos e possui ao menos um card futuro elegível
- **THEN** a resposta diária retorna `overdueCards` igual a zero, `currentCard` nulo e `canAdvance` igual a verdadeiro

#### Scenario: Fila sem cards elegíveis
- **WHEN** a pessoa não possui cards vencidos nem cards futuros elegíveis
- **THEN** a resposta diária retorna `currentCard` nulo e `canAdvance` igual a falso

### Requirement: Revisão antecipada explícita
O sistema SHALL permitir iniciar uma sessão de avanço somente por ação explícita da pessoa e somente quando não houver cards vencidos. A fila antecipada SHALL selecionar cards futuros sem alterar a regra de prioridade da fila diária.

#### Scenario: Início de avanço permitido
- **WHEN** a pessoa inicia uma revisão antecipada sem cards vencidos e há card futuro elegível
- **THEN** a API retorna um único card futuro como card atual em sessão de avanço

#### Scenario: Início de avanço bloqueado por pendência
- **WHEN** a pessoa tenta iniciar uma revisão antecipada enquanto há card vencido
- **THEN** a API rejeita a operação com conflito de estado e não cria sessão de avanço

### Requirement: Interface de fila contínua
A interface SHALL exibir o total de cards vencidos e SHALL substituir o card exibido pelo próximo card devolvido após cada classificação. Quando a fila vencida estiver vazia, a interface SHALL apresentar uma ação de revisão antecipada somente se a API indicar que ela está disponível.

#### Scenario: Próximo card na interface
- **WHEN** a classificação é confirmada pela API com um próximo card
- **THEN** a interface apresenta esse card sem navegar por uma lista pré-carregada

#### Scenario: Convite de avanço na interface
- **WHEN** a API informa fila vencida vazia e avanço disponível
- **THEN** a interface exibe uma ação para iniciar a revisão antecipada
