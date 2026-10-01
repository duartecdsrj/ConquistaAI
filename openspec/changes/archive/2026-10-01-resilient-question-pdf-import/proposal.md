## Why

A importação PDF pode deixar metadados no enunciado, perder o cargo exibido no cabeçalho e ficar travada após falhas do worker. A extração visual antecipada e reanálise repetida tornam lotes grandes lentos.

## What Changes

- Recuperar jobs órfãos e evitar reanálise de candidatos persistidos.
- Extrair cabeçalhos explícitos para metadados e removê-los do enunciado.
- Associar figuras por marcador no ponto semântico e mostrar início/fim do job no histórico.
- Reduzir trabalho visual e contexto repetido.

## Capabilities

### New Capabilities

- `resilient-question-pdf-import`: retomada, metadados, figuras, desempenho e histórico da importação PDF.

### Modified Capabilities

- Nenhuma.

## Impact

Worker, analisador Codex, persistência, API, SPA administrativa e documentação.
