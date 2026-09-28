## 1. Seleção de evidências no worker

- [x] 1.1 Extrair do snapshot o primeiro parágrafo útil do enunciado e uma alternativa central não vazia, sem registrar seus conteúdos.
- [x] 1.2 Pesquisar as duas referências no PDF de origem, incorporar suas janelas de páginas e preservar as buscas explícita e de gabarito.
- [x] 1.3 Registrar hashes, resultados e páginas finais das buscas automáticas em logs estruturados seguros.

## 2. Validação e documentação

- [x] 2.1 Cobrir a composição automática, a ausência de correspondência e a união deduplicada de evidências com testes focados do worker.
- [x] 2.2 Atualizar `docs/API.md` para explicitar a seleção automática de páginas sem mudança de payload.
- [x] 2.3 Executar as validações PHP aplicáveis e `git diff --check`.
