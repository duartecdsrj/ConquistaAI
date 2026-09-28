## 1. Contrato e persistência Arena

- [x] 1.1 Documentar as rotas, payloads, paginação e autorização de salas públicas, salas privadas do criador e entrada pública.
- [x] 1.2 Criar migration reversível e modelo de visibilidade `PUBLIC`/`PRIVATE`, preservando salas existentes como privadas.
- [x] 1.3 Estender DTOs, entidade, mapper e portas de repositório com visibilidade e resumos tipados de sala.

## 2. Casos de uso e HTTP

- [x] 2.1 Implementar criação, entrada pública, descoberta paginada e listagem privada do criador com QueryBuilder Doctrine e autorização.
- [x] 2.2 Expor assuntos canônicos disponíveis à Arena e preservar a confirmação de prontidão antes do início.
- [x] 2.3 Adicionar controladores finos, fábricas de requisição e rotas autenticadas no envelope padrão.

## 3. Experiência Quasar Arena

- [x] 3.1 Atualizar módulos DDD/Axios/composable para visibilidade, listagens, entrada pública e assuntos disponíveis.
- [x] 3.2 Implementar criação pública/privada, descoberta, salas privadas do criador, seleção de assuntos e confirmação de prontidão na página Arena.
- [x] 3.3 Restaurar a sala pelo parâmetro de URL e tratar carregamento, erro, vazio, lotação e conflito sem dados simulados.
- [x] 3.4 Atualizar `docs/FRONTEND.md` com o fluxo Arena.

## 4. Validação integrada

- [x] 4.1 Cobrir serviços e repositório para visibilidade, descoberta, entrada pública, isolamento privado e prontidão.
- [x] 4.2 Cobrir API/controlador e fluxos de interface proporcionais ao risco.
- [x] 4.3 Executar migrations, lint PHP, testes Arena, build frontend e `git diff --check`.
