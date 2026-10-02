## 1. Contrato e domínio

- [x] 1.1 Documentar as rotas e DTOs de fila incremental e avanço em `docs/API.md`.
- [x] 1.2 Modelar o modo de avanço e os DTOs tipados de estado da fila.
- [x] 1.3 Estender as portas de repositório para contagem, seleção incremental e exclusão de cards já atendidos na sessão.

## 2. Backend

- [x] 2.1 Implementar seleção Doctrine de cards vencidos e futuros por usuário, com prioridade e isolamento.
- [x] 2.2 Ajustar os casos de uso de abertura e classificação para devolver o próximo estado da fila e concluir a sessão quando aplicável.
- [x] 2.3 Ajustar controllers, mappers e composição de dependências ao novo contrato.
- [ ] 2.4 Cobrir os cenários de fila, conflito de avanço, retorno do próximo card e idempotência com testes focais.

## 3. Frontend

- [x] 3.1 Atualizar os contratos Domain/Application/Infrastructure de Review para o estado contínuo da fila.
- [x] 3.2 Atualizar `useReview` e a página Quasar para trocar o card pela resposta da API, mostrar vencidos e oferecer avanço condicional.
- [x] 3.3 Documentar a experiência de fila contínua em `docs/FRONTEND.md`.

## 4. Validação e registro

- [x] 4.1 Executar lint e testes focais do backend, build tipado da SPA e `git diff --check`.
- [x] 4.2 Atualizar `docs/DEVELOPMENT_STATE.md` com entrega, decisões, validações, pendências e próximo passo.
