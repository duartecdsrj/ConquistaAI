## Why

A alternativa escolhida como evidência pode conter texto curto e recorrente, ampliando a busca para centenas de páginas e consumindo o prazo global antes da análise pelo Codex. O worker precisa usar uma referência mais distintiva do próprio enunciado.

## What Changes

- Remove a alternativa automática da busca de evidências.
- Seleciona, entre as linhas do enunciado, o trecho útil mais longo após remover caracteres especiais apenas das extremidades.
- Mantém as buscas explícita e de gabarito como evidências complementares.

## Capabilities

### New Capabilities

Nenhuma.

### Modified Capabilities

- `execucao-resiliente-do-worker-de-correcao`: altera a referência automática do snapshot usada para localizar páginas do PDF.

## Impact

Afeta o seletor de evidências, o worker de correção, seus testes e a descrição do comportamento em `docs/API.md`; não altera rotas nem payloads.
