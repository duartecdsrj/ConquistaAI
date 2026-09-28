## ADDED Requirements

### Requirement: Descoberta segura de salas Arena
O sistema SHALL fornecer listagem paginada de salas públicas `WAITING` para qualquer usuário autenticado e listagem paginada de salas privadas `WAITING` cujo criador seja o usuário autenticado. Cada item SHALL conter somente identificador, visibilidade, capacidade, contagem de participantes, configuração, status e data de criação; códigos privados e dados de participantes SHALL NOT ser expostos na descoberta pública.

#### Scenario: Usuário consulta salas públicas
- **WHEN** um usuário autenticado consulta as salas públicas
- **THEN** recebe apenas salas `PUBLIC` em `WAITING`, paginadas pelo contrato padrão

#### Scenario: Criador consulta salas privadas
- **WHEN** um usuário autenticado consulta suas salas privadas criadas
- **THEN** recebe apenas salas `PRIVATE` em `WAITING` cujo criador é ele

#### Scenario: Terceiro consulta salas privadas
- **WHEN** um usuário autenticado não criador consulta suas salas privadas
- **THEN** não recebe salas privadas criadas por outros usuários
