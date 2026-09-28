## ADDED Requirements

### Requirement: Importação inicial da foto Google
O sistema SHALL tentar importar para armazenamento próprio a foto declarada no campo `picture` de uma identidade Google OIDC já validada quando criar uma conta pelo primeiro acesso Google. A importação deve aceitar somente origem HTTPS em domínio Google permitido, validar o conteúdo da imagem e não pode impedir a criação da conta quando a foto estiver ausente ou for recusada.

#### Scenario: Foto Google válida no primeiro acesso
- **WHEN** uma pessoa realiza o primeiro acesso Google com uma foto `picture` válida
- **THEN** o sistema cria a conta pendente e armazena uma cópia validada da foto como avatar inicial do usuário

#### Scenario: Foto Google indisponível ou inválida
- **WHEN** a foto declarada pela identidade Google não pode ser baixada ou não satisfaz as validações de segurança
- **THEN** o sistema cria a conta pendente sem avatar e não registra a URL nem bloqueia o fluxo de acesso

### Requirement: Foto manual do próprio usuário
O sistema SHALL permitir que um usuário `ACTIVE` autenticado consulte e substitua somente sua própria foto de perfil por imagem válida enviada ao sistema. A substituição deve persistir a nova imagem de forma atômica, preservar o avatar anterior até a confirmação e não expor caminhos internos de armazenamento.

#### Scenario: Usuário substitui a foto de perfil
- **WHEN** um usuário ativo envia uma imagem válida pela experiência de perfil
- **THEN** a API armazena a foto normalizada, passa a devolvê-la como foto do usuário e deixa de usar a cópia inicial do Google

#### Scenario: Arquivo de foto inválido
- **WHEN** um usuário envia arquivo que não é uma imagem permitida ou excede os limites definidos
- **THEN** a API responde `422 VALIDATION_FAILED` e mantém a foto anterior inalterada

### Requirement: Acesso protegido ao avatar
O sistema MUST entregar o binário do avatar somente por rota autenticada ao proprietário ou a um administrador autorizado. URLs de origem Google, credenciais externas e caminhos físicos não podem aparecer nas respostas da API, eventos ou logs.

#### Scenario: Outro usuário tenta ler avatar privado
- **WHEN** um usuário autenticado sem privilégio administrativo solicita a foto de outro usuário
- **THEN** a API responde `404 RESOURCE_NOT_FOUND` sem revelar a existência ou o local do arquivo

#### Scenario: Nova autenticação Google após foto manual
- **WHEN** um usuário que já substituiu sua foto pelo sistema autentica novamente pelo Google
- **THEN** o sistema mantém a foto manual e não baixa nem sobrescreve o avatar existente
