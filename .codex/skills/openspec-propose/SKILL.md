---
name: openspec-propose
description: Cria proposta OpenSpec enxuta em pt-BR para mudança relevante de produto ou arquitetura.
---

Use quando o pedido exigir mudança relevante. Antes, leia `openspec/README.md` e os arquivos obrigatórios indicados nele.

1. Derive nome curto em kebab-case e execute `openspec new change <nome>`.
2. Use `openspec status --change <nome> --json` e `openspec instructions <artefato> --change <nome> --json`.
3. Crie somente os artefatos exigidos para aplicar: proposta, specs delta, design apenas se houver decisão técnica, e tarefas.
4. Tudo em pt-BR, objetivo e verificável. Não copie regras do projeto nem invente escopo.
5. Mostre nome, arquivos e próximo comando: `/opsx:apply <nome>`.
