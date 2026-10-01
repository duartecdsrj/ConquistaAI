## Why

Jobs longos permanecem limitados pela execução Codex e podem reanalisar o mesmo checkpoint quando o lease fixo expira durante uma chamada ainda ativa. A consolidação concorrente também pode gravar estado terminal com progresso parcial.

## What Changes

- Renovar o lease do lote enquanto o executor Codex estiver ativo e impedir conclusão de execução que perdeu a posse.
- Vincular a execução externa ao checkpoint/lote para cancelar trabalho obsoleto e limitar concorrência efetiva por job.
- Consolidar estado e progresso do job sob lock transacional a partir do mesmo resumo terminal.
- Remover contexto de taxonomia repetido entre candidatos de um lote, mantendo análise Codex integral para cada candidato.

## Capabilities

### New Capabilities

- `codex-import-runner-efficiency`: supervisão de lease, execução externa e contexto compartilhado para importação PDF por Codex.

### Modified Capabilities

- `resilient-parallel-pdf-import`: renovação de posse e consolidação terminal atômica de checkpoints.
- `batched-question-pdf-analysis`: contexto compartilhado por lote sem reduzir candidatos ou validação individual.
