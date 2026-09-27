---
name: openspec-apply-change
description: Implementa uma mudança OpenSpec com contexto mínimo, em pt-BR.
---

Use para implementar mudança OpenSpec. Leia `openspec/README.md` e rode `openspec status --change <nome> --json` e `openspec instructions apply --change <nome> --json`.

Leia somente os arquivos retornados em `contextFiles`. Execute uma tarefa pendente por vez, valide proporcionalmente, marque `- [x]` imediatamente e atualize `docs/DEVELOPMENT_STATE.md`. Pare apenas por bloqueio real ou decisão de produto. Não reabra arquivos, não repita contexto e não comece outra mudança antes de concluir a atual.
