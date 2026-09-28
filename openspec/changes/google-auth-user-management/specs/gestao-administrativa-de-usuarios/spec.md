## ADDED Requirements

### Requirement: Listagem administrativa paginada de usuários
O sistema SHALL disponibilizar a administradores uma listagem paginada de usuários com página padrão 1, `per_page` padrão 25 e máximo 100. Cada item deve informar identificador, nome, e-mail local, e-mail Google associado quando houver, papéis e status, sem expor hash de senha, tokens ou eventos internos.

#### Scenario: Administrador consulta usuários
- **WHEN** um administrador abre a gestão de usuários com filtros válidos de texto ou status
- **THEN** a API retorna somente os itens da página solicitada no envelope padrão com metadados de paginação

#### Scenario: Usuário sem privilégio consulta gestão
- **WHEN** um usuário sem papel `ADMIN` solicita a listagem administrativa
- **THEN** a API responde `403 FORBIDDEN` e não revela dados de outros usuários

### Requirement: Cadastro administrativo de usuário
O sistema SHALL permitir que administradores cadastrem um usuário com nome, e-mail local, papéis, status e, opcionalmente, senha local e e-mail Google associado. Nome, e-mails e papéis devem ser validados e os e-mails local e Google devem ser únicos nos respectivos contratos.

#### Scenario: Cadastro com e-mail Google associado
- **WHEN** um administrador cadastra um usuário válido com e-mail Google verificado informado para associação
- **THEN** o sistema cria o usuário com os papéis e status escolhidos e reserva o e-mail Google para esse usuário

#### Scenario: Cadastro com dados duplicados
- **WHEN** um administrador tenta cadastrar e-mail local ou e-mail Google já associado de forma incompatível
- **THEN** a API responde `409 STATE_CONFLICT` sem criar usuário parcial

### Requirement: Associação administrativa de identidade Google
O sistema SHALL permitir somente a administradores associar, substituir ou remover o e-mail Google de um usuário. A operação deve validar o formato, normalizar o e-mail, manter unicidade e registrar auditoria sem informações secretas.

#### Scenario: Associação a usuário existente
- **WHEN** um administrador associa um e-mail Google ainda disponível a um usuário existente
- **THEN** a próxima autenticação Google válida desse e-mail é resolvida para o usuário associado

#### Scenario: Associação já utilizada
- **WHEN** um administrador tenta associar a um usuário um e-mail Google vinculado a outro usuário
- **THEN** a API responde `409 STATE_CONFLICT` sem alterar nenhum dos vínculos

### Requirement: Administração de papéis e status
O sistema SHALL permitir somente a administradores alterar papéis e status de usuários. A alteração deve ser transacional, auditada e preservar ao menos um usuário `ADMIN` ativo.

#### Scenario: Liberação de usuário pendente
- **WHEN** um administrador promove um usuário de `PENDING_APPROVAL` para `ACTIVE`
- **THEN** a alteração é persistida e o usuário pode autenticar nas tentativas posteriores

#### Scenario: Proteção do último administrador ativo
- **WHEN** uma alteração removeria ou restringiria o último usuário com papel `ADMIN` e status `ACTIVE`
- **THEN** a API responde `409 STATE_CONFLICT` e preserva o estado anterior

### Requirement: Experiência administrativa de usuários
A SPA SHALL oferecer uma seção administrativa de usuários por meio das camadas Domain, Application, Infrastructure e Interface/Http. A tela deve usar componentes Quasar e tratar carregamento, erro, vazio, filtros, paginação, cadastro, associação Google e alteração de status/papéis com dados reais da API.

#### Scenario: Estado vazio da gestão
- **WHEN** os filtros administrativos não retornam usuários
- **THEN** a tela apresenta estado vazio claro sem dados simulados

#### Scenario: Falha em uma alteração administrativa
- **WHEN** a API recusa uma alteração de usuário
- **THEN** a tela exibe a mensagem segura devolvida pela API e conserva os dados confirmados anteriormente
