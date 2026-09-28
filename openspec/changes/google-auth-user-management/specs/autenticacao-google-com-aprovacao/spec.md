## ADDED Requirements

### Requirement: Entrada com conta Google verificada
O sistema SHALL disponibilizar na tela de entrada uma ação para autenticação Google configurada. A API SHALL aceitar a credencial OIDC somente após validar assinatura, emissor, público, expiração e declaração `email_verified`; credenciais inválidas não podem criar usuário nem sessão.

#### Scenario: Credencial Google válida de usuário ativo vinculado
- **WHEN** uma pessoa conclui a autenticação Google com e-mail verificado vinculado a um usuário `ACTIVE`
- **THEN** a API emite a mesma sessão de access token e refresh cookie usada pelo login local e devolve o usuário autenticado

#### Scenario: Credencial Google inválida
- **WHEN** a API recebe uma credencial expirada, de público diferente, sem e-mail verificado ou com assinatura inválida
- **THEN** ela responde `401 UNAUTHENTICATED` sem criar ou alterar usuário, vínculo ou sessão

### Requirement: Criação pendente no primeiro acesso Google
O sistema SHALL criar exatamente um usuário com status `PENDING_APPROVAL`, papel `USER`, sem senha local e vínculo ao e-mail Google verificado quando não houver vínculo para essa identidade. A resposta não pode conter access token nem refresh token enquanto o usuário não estiver ativo.

#### Scenario: Primeiro acesso por Google
- **WHEN** uma pessoa autenticada pelo Google não possui vínculo de identidade no sistema
- **THEN** o sistema cria a conta pendente uma única vez e informa que o acesso às funcionalidades depende de liberação administrativa

#### Scenario: Nova tentativa de conta pendente
- **WHEN** a pessoa com conta `PENDING_APPROVAL` volta a autenticar pelo mesmo e-mail Google
- **THEN** o sistema não cria conta adicional, não emite sessão e reapresenta a mensagem de aguardo de liberação

### Requirement: Vínculo Google não é inferido pelo e-mail local
O sistema MUST tratar o vínculo Google como identidade externa explícita e única. A coincidência entre o e-mail declarado pela conta Google e o e-mail local de uma conta existente não pode conceder acesso nem criar vínculo automaticamente.

#### Scenario: E-mail Google coincide com conta local sem vínculo
- **WHEN** uma credencial Google válida declara o mesmo e-mail de um usuário local sem e-mail Google associado
- **THEN** o sistema responde `409 STATE_CONFLICT`, não cria vínculo nem conta adicional e exige associação administrativa antes de conceder acesso Google

### Requirement: Restrição por status de conta
O sistema SHALL emitir sessões funcionais somente para usuários `ACTIVE`, independentemente de a autenticação ser por senha ou Google. Usuários pendentes ou bloqueados devem receber resposta segura sem tokens e a interface deve comunicar o motivo permitido.

#### Scenario: Usuário pendente tenta usar senha
- **WHEN** um usuário `PENDING_APPROVAL` envia credenciais locais corretas
- **THEN** a API não emite sessão e a interface informa que a liberação administrativa é necessária

#### Scenario: Administrador libera conta pendente
- **WHEN** um administrador altera o status de um usuário pendente para `ACTIVE`
- **THEN** a próxima autenticação válida desse usuário emite uma sessão funcional

### Requirement: Preservação segura de credenciais externas
O sistema MUST não persistir, registrar em logs ou expor em respostas a credencial OIDC do Google, tokens de acesso do Google ou segredos de cliente.

#### Scenario: Auditoria de autenticação Google
- **WHEN** uma autenticação Google é aceita ou recusada
- **THEN** o evento de auditoria registra somente dados operacionais seguros e não inclui a credencial recebida nem tokens do Google
