## ADDED Requirements

### Requirement: Notificação de conclusão de correção
O sistema SHALL persistir e entregar ao solicitante uma notificação autenticada quando sua correção alcançar `PROPOSED` ou `FAILED`.

#### Scenario: Proposta concluída com usuário em outra tela
- **WHEN** o worker concluir uma proposta para usuário conectado
- **THEN** o cliente recebe evento global com atalho para revisar a correção

#### Scenario: Usuário desconectado
- **WHEN** o worker concluir uma proposta sem conexão WebSocket ativa
- **THEN** a notificação permanece persistida para recuperação posterior
