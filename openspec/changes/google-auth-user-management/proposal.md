## Why

O acesso atual depende de senha e não permite que a equipe controle a liberação de pessoas que chegam pelo Google. Administradores precisam cadastrar, associar e aprovar usuários sem conceder acesso funcional antes dessa decisão.

## What Changes

- Adicionar autenticação com Google, vinculada a um e-mail Google verificado e às sessões JWT/refresh já adotadas pela API.
- Criar automaticamente um usuário com status pendente quando o primeiro acesso Google não corresponder a uma conta existente, impedindo o uso das funcionalidades até a aprovação administrativa.
- Exibir, na experiência de entrada e enquanto houver sessão pendente, feedback claro de que a liberação por administrador é necessária.
- Disponibilizar gestão administrativa paginada de usuários, incluindo cadastro, associação de e-mail Google, papéis e alteração de status.
- Importar de forma segura a foto de perfil declarada pela identidade Google ao criar a conta e permitir que cada usuário ativo substitua sua própria foto pelo sistema.
- Preservar o login por e-mail e senha e impedir associação implícita baseada somente em e-mails sem identidade Google verificada.

## Capabilities

### New Capabilities

- `autenticacao-google-com-aprovacao`: autentica identidades Google verificadas, cria solicitações pendentes de acesso e emite sessões apenas para contas ativas.
- `gestao-administrativa-de-usuarios`: permite a administradores listar, cadastrar, associar e-mails Google e liberar ou restringir usuários.
- `foto-de-perfil-do-usuario`: armazena a foto inicial do Google e permite que o usuário autenticado consulte e substitua sua foto de perfil.

### Modified Capabilities

- Nenhuma.

## Impact

- Backend Identity: entidade e persistência de usuário/identidade externa e avatar, OAuth/OIDC do Google, armazenamento de imagens, rotas de autenticação, perfil e administração, DTOs, serviços, mappers, autorização e auditoria de eventos.
- Frontend Identity e nova área administrativa de usuários: contratos de domínio, casos de uso, repositórios Axios, composables e telas Quasar para entrada, estado pendente, perfil, listagem e manutenção.
- Documentação: `docs/API.md`, `docs/FRONTEND.md` e `docs/DEVELOPMENT_STATE.md`.
- Configuração segura do servidor para credenciais OAuth do Google e URL de retorno autorizada.
