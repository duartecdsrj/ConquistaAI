## Why

A análise atual cria um executor Codex por candidato, o que transforma PDFs longos em milhares de inicializações isoladas e torna a importação muito mais lenta do que a capacidade de análise do modelo permite.

## What Changes

- Agrupar candidatos independentes de um mesmo PDF em requisições estruturadas limitadas por quantidade e tamanho configuráveis.
- Validar a resposta individual de cada candidato do lote e preservar checkpoint, retry e escrita idempotente por candidato.
- Registrar telemetria real do lote e distribuí-la de forma auditável entre as análises individuais.
- Manter imagens, marcadores e regras de metadados, gabarito e taxonomia já aplicadas à análise unitária.

## Capabilities

### New Capabilities

- `batched-question-pdf-analysis`: análise estruturada de múltiplos candidatos PDF em uma única execução do provider.

### Modified Capabilities

- `resilient-parallel-pdf-import`: checkpoints passam a ser reivindicados e concluídos em grupos, sem perder isolamento de falhas por candidato.

## Impact

Afeta o contrato interno do analisador, executor Codex, worker de importação, telemetria de jobs, configuração Compose e testes da importação PDF; não altera endpoints HTTP públicos.
