## ADDED Requirements

### Requirement: Checkpoints concorrentes de candidatos PDF
O sistema SHALL materializar e reivindicar atomicamente cada candidato de um job PDF, permitindo que múltiplos consumidores processem candidatos distintos do mesmo job sem criar questão ou análise duplicada.

#### Scenario: Dois consumidores processam o mesmo PDF
- **WHEN** dois consumidores ativos reivindicarem candidatos pendentes do mesmo job
- **THEN** cada candidato será atribuído a no máximo um consumidor por vez e os checkpoints persistidos impedirão análise duplicada

### Requirement: Falhas isoladas e retomáveis
O sistema SHALL distinguir resposta inválida de candidato de indisponibilidade transitória do analisador; a primeira SHALL marcar somente o candidato como falho e a segunda SHALL aplicar backoff limitado ao candidato sem encerrar prematuramente o job.

#### Scenario: Resposta inválida para um candidato
- **WHEN** o analisador retornar payload que não passa na validação
- **THEN** o checkpoint será marcado como falho com mensagem segura e os demais candidatos continuarão sendo processados

#### Scenario: Executor temporariamente indisponível
- **WHEN** o executor do analisador falhar de forma transitória
- **THEN** somente o candidato será reenfileirado com próxima tentativa e o job permanecerá recuperável

### Requirement: Timestamps e resumo terminal do job
O sistema SHALL registrar `startedAt` uma única vez na primeira reivindicação efetiva e SHALL registrar `finishedAt` somente quando o job alcançar estado terminal.

#### Scenario: Checkpoint durante processamento
- **WHEN** um candidato concluir, falhar ou for reenfileirado
- **THEN** o `startedAt` original do job será preservado e `finishedAt` continuará nulo enquanto existir trabalho não terminal

#### Scenario: Último candidato termina
- **WHEN** todos os checkpoints de um job estiverem em estado terminal
- **THEN** o job será consolidado como `COMPLETED` ou `FAILED` com `finishedAt` posterior ou igual a `startedAt`
