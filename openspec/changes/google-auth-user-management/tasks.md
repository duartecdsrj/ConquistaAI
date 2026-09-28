## 1. Contratos e fundação de Identity

- [x] 1.1 Documentar em `docs/API.md` os contratos de autenticação Google, resposta de acesso pendente e endpoints administrativos de usuários.
- [x] 1.2 Atualizar `docs/FRONTEND.md` com o módulo administrativo de usuários e o fluxo de autenticação/aprovação Google.
- [x] 1.3 Criar migration reversível para senha local opcional, estados de acesso, vínculo Google único e metadados de avatar, preservando os usuários atuais ativos.
- [x] 1.4 Modelar entidades, value objects, enumerações e interfaces de repositório do contexto Identity para vínculo Google e administração de usuários.
- [x] 1.5 Implementar repositórios Doctrine, armazenamento privado de avatar, transações e persistência de eventos de auditoria sem credenciais ou tokens externos.

## 2. Autenticação Google e restrição de acesso

- [x] 2.1 Implementar o adaptador de validação OIDC do Google com verificação de assinatura, emissor, público, expiração e e-mail confirmado.
- [x] 2.2 Implementar o caso de uso de autenticação Google para resolver vínculo explícito, criar conta pendente e emitir sessão somente a usuário ativo.
- [x] 2.3 Ajustar os casos de uso de login, refresh e consulta de sessão para aplicar os estados de usuário e devolver feedback seguro sem tokens quando aplicável.
- [x] 2.4 Criar Request/Response DTOs, mappers, controlador fino e rota `POST /auth/google` usando o envelope padrão.
- [x] 2.5 Cobrir com testes unitários e de integração a validação OIDC, criação idempotente pendente, colisão com conta local e bloqueio de sessão.
- [x] 2.6 Importar a foto `picture` da identidade Google somente na criação da conta, com origem HTTPS permitida, limites, validação e falha não bloqueante.

## 3. Administração de usuários no backend

- [x] 3.1 Implementar casos de uso para listar usuários paginados, cadastrar, associar/remover e-mail Google e alterar papéis ou status.
- [x] 3.2 Garantir no serviço a unicidade de identidade Google, a transação das alterações e a proteção do último administrador ativo.
- [x] 3.3 Criar DTOs, mappers, controladores e rotas administrativas com autorização `ADMIN` e respostas `403`, `409` e `422` padronizadas.
- [x] 3.4 Cobrir com testes de controller, service e repositório as permissões, paginação, conflitos, auditoria e liberação de usuário pendente.

## 4. Experiência SPA

- [x] 4.1 Implementar o adaptador de infraestrutura para Google Identity Services e o comando tipado de login Google no módulo Identity.
- [x] 4.2 Atualizar `useAuth` e a tela de entrada Quasar para iniciar a autenticação Google, apresentar falhas seguras e manter visível o aviso de aprovação pendente.
- [x] 4.3 Criar o módulo DDD de gestão de usuários (portas, DTOs, casos de uso, repositório Axios e composição no container).
- [x] 4.4 Criar a seção Quasar administrativa com listagem, filtros, paginação, estados de carregamento/erro/vazio e formulários de cadastro, vínculo Google, papéis e status.
- [x] 4.5 Cobrir os fluxos de login Google, acesso pendente e administração de usuários com testes proporcionais de interface.

## 5. Foto de perfil do usuário

- [x] 5.1 Criar contratos de domínio, DTOs, mapper, serviço, repositório Doctrine e rotas autenticadas para consultar e substituir a foto do próprio usuário.
- [x] 5.2 Validar uploads de imagem, normalizar o arquivo e substituir a foto anterior de forma atômica, sem expor caminhos internos.
- [x] 5.3 Criar o repositório Axios, caso de uso, composable e experiência Quasar de perfil para visualizar e alterar a própria foto.
- [x] 5.4 Cobrir autorização por proprietário, leitura administrativa quando necessária, importação Google, substituição manual e rejeição de arquivo inválido.

## 6. Configuração, validação e continuidade

- [x] 6.1 Documentar as variáveis de ambiente e os domínios/origens autorizados do cliente Google sem expor segredos.
- [x] 6.2 Executar migrations em ambiente controlado e verificar que usuários existentes permanecem ativos e conseguem usar login local.
- [x] 6.3 Executar testes da API, build/testes da SPA e verificações estáticas; registrar os resultados em `docs/DEVELOPMENT_STATE.md`.
- [x] 6.4 Revisar a configuração Google Cloud com os domínios e origens confirmados antes de habilitar o fluxo em produção.
