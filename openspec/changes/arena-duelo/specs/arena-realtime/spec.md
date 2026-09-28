## ADDED Requirements

### Requirement: Sincronização e reconexão
O sistema SHALL emitir atualizações somente para participantes autenticados da sala e SHALL permitir recuperar seu estado após reconectar sem alterar partida, resposta ou pontuação.

#### Scenario: Refresh na questão
- **WHEN** participante atualiza a página com questão aberta
- **THEN** recebe a questão corrente e seu estado definitivo de resposta

#### Scenario: Evento de sala
