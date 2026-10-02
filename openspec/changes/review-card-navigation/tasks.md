## 1. Contrato e persistência

- [x] 1.1 Documentar navegação e o estado adicional da sessão em `docs/API.md` e `docs/FRONTEND.md`.
- [x] 1.2 Adicionar a posição atual da sessão em migration, entidade e repositório Doctrine, mantendo compatibilidade com sessões existentes.

## 2. Backend

- [x] 2.1 Modelar DTOs, caso de uso e endpoint autenticado para navegar para card anterior ou próximo.
- [x] 2.2 Ajustar a seleção incremental, classificação e mapeamento da sessão para posição e disponibilidade de navegação.
- [x] 2.3 Cobrir validação do payload de navegação e preservar os testes focais existentes de Review; cenários de integração da seleção seguem cobertos pelo contrato de sessão.

## 3. Frontend

- [x] 3.1 Atualizar contratos Domain/Application/Infrastructure de Review para a navegação.
- [x] 3.2 Integrar os controles Quasar Anterior e Próximo na sessão.

## 4. Validação e registro

- [x] 4.1 Executar validações focais do backend, build tipado da SPA e `git diff --check`.
- [x] 4.2 Atualizar `docs/DEVELOPMENT_STATE.md` com entrega, decisões, validações, pendências e próximo passo.
