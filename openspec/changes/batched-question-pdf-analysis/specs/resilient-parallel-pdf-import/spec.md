## ADDED Requirements

### Requirement: Checkpoints agrupados sem perda de isolamento
O sistema SHALL reivindicar e processar checkpoints compatíveis em grupo, mantendo transição, retry, conclusão e consolidação individual por candidato.

#### Scenario: Falha transitória de execução em lote
- **WHEN** a execução externa de um lote falhar antes de devolver resultados
- **THEN** somente os checkpoints reivindicados naquele lote serão reenfileirados com backoff, e checkpoints de outros jobs permanecerão inalterados
