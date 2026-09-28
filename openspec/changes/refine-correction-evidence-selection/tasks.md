## 1. Seleção determinística do enunciado

- [x] 1.1 Substituir a referência automática por uma linha útil mais longa do enunciado, removendo somente caracteres especiais das extremidades.
- [x] 1.2 Remover a alternativa da composição automática e preservar as buscas explícita, de gabarito e páginas de origem.
- [x] 1.3 Manter logs seguros com hash, páginas encontradas e evidências finais da única referência automática.

## 2. Validação e contrato

- [x] 2.1 Cobrir linha mais longa, limpeza de contorno, empate determinístico, ausência de linha e ausência de busca com testes focados.
- [x] 2.2 Atualizar `docs/API.md` sem alterar payloads.
- [x] 2.3 Executar lint PHP, testes focados e `git diff --check`.
