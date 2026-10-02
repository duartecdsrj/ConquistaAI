## MODIFIED Requirements

### Requirement: Fila de revisão incremental
O sistema SHALL entregar no máximo um card atual por sessão de revisão, SHALL selecionar o próximo card dinamicamente e SHALL manter a posição do card exibido entre os cards já servidos. A seleção diária SHALL conter apenas cards vencidos e respeitar o usuário autenticado.

#### Scenario: Início com cards vencidos
- **WHEN** a pessoa inicia uma revisão diária com cards vencidos
- **THEN** a API retorna um único card atual e a quantidade total de cards vencidos

#### Scenario: Classificação retorna próximo card
- **WHEN** a pessoa classifica o card atual de uma sessão diária que ainda possui cards vencidos elegíveis
- **THEN** a API registra uma única revisão imutável e retorna o próximo card atual selecionado após a atualização do progresso

#### Scenario: Próximo card sem classificação
- **WHEN** a pessoa solicita o próximo card de uma sessão ativa sem classificar o card atual
- **THEN** a API mantém o card atual sem evento de revisão e retorna o próximo card já servido ou um novo card elegível da mesma fila

### Requirement: Estado observável da fila
O sistema SHALL retornar em toda leitura, navegação ou atualização da sessão a quantidade de cards vencidos, o card atual anulável, a elegibilidade para revisão antecipada e as possibilidades de navegar para o card anterior ou próximo. A API SHALL informar elegibilidade de avanço somente quando não houver cards vencidos e existir ao menos um card futuro elegível para o usuário.

#### Scenario: Fila vencida vazia com avanço disponível
- **WHEN** a pessoa não possui cards vencidos e possui ao menos um card futuro elegível
- **THEN** a resposta diária retorna `overdueCards` igual a zero, `currentCard` nulo e `canAdvance` igual a verdadeiro

#### Scenario: Fila sem cards elegíveis
- **WHEN** a pessoa não possui cards vencidos nem cards futuros elegíveis
- **THEN** a resposta diária retorna `currentCard` nulo e `canAdvance` igual a falso

#### Scenario: Card previamente classificado
- **WHEN** a pessoa retorna a um card já classificado na mesma sessão
- **THEN** a API informa que o card não aceita nova classificação e mantém os controles de navegação disponíveis

## ADDED Requirements

### Requirement: Navegação auditável da sessão
O sistema SHALL permitir navegar apenas entre cards já servidos na sessão e servir um novo card somente pela ação explícita de próximo. A navegação SHALL ser limitada ao usuário dono da sessão e não SHALL criar evento de revisão.

#### Scenario: Retorno ao card anterior
- **WHEN** a pessoa solicita o card anterior e existe uma posição anterior servida
- **THEN** a API retorna esse card sem criar, alterar ou remover classificação

#### Scenario: Limite de próximo card
- **WHEN** a pessoa solicita próximo e não existe card servido posterior nem candidato elegível
- **THEN** a API mantém o card atual e informa que não há próximo card disponível

### Requirement: Interface de navegação de revisão
A interface SHALL exibir controles Anterior e Próximo junto à sessão ativa e SHALL usar exclusivamente o estado devolvido pela API para habilitá-los. A interface SHALL ocultar as classificações quando o card atual já tiver sido classificado na sessão.

#### Scenario: Navegação sem resposta
- **WHEN** a pessoa toca em Próximo antes de classificar
- **THEN** a interface apresenta o card devolvido pela API sem enviar uma classificação

#### Scenario: Card classificado revisitado
- **WHEN** a pessoa retorna a um card classificado
- **THEN** a interface informa que ele já foi classificado e não apresenta botões para registrar uma segunda classificação
