## Context

O contexto Identity hoje emite sessões JWT/refresh para usuários ativos com senha. Usuários possuem e-mail, nome, hash de senha, status e papéis, mas não há identidade externa nem interface administrativa de usuários. A mudança acrescenta um provedor externo, novos estados de acesso e uma área administrativa; portanto, precisa preservar o fluxo de sessão e os limites de camadas definidos em `AGENTS.md`.

## Goals / Non-Goals

**Goals:**

- Autenticar somente credenciais OIDC do Google verificadas pela API.
- Vincular uma identidade Google a exatamente um usuário e manter o acesso funcional condicionado ao status `ACTIVE`.
- Criar registros pendentes de aprovação para primeiras entradas Google e permitir a decisão administrativa auditável.
- Entregar gerenciamento administrativo real pela API e pela SPA, incluindo paginação, carregamento, vazio e erro.

**Non-Goals:**

- Remover ou alterar o login local já existente.
- Permitir autoaprovação, atribuição automática de `ADMIN`, associação por e-mail não verificado ou vinculação automática de contas existentes.
- Sincronizar continuamente foto, grupos, contatos ou outros dados da conta Google.
- Implementar recuperação de senha, convite por e-mail ou outros provedores sociais nesta mudança.

## Decisions

### Credencial Google validada no backend

A SPA usará o Google Identity Services somente para obter a credencial OIDC. Ela a encaminhará a `POST /auth/google`; um adaptador de infraestrutura validará assinatura/JWKS, `iss`, `aud`, expiração e `email_verified` antes de entregar uma identidade tipada ao caso de uso. Isso não confia no nome ou e-mail enviados pelo navegador e mantém segredos e a política de identidade no servidor.

Alternativa considerada: redirecionamento OAuth com callback da API. Foi descartada para esta entrega por introduzir armazenamento de `state`, retorno cross-origin e troca de código, embora possa substituir o adaptador sem mudar os contratos de domínio futuramente.

### Vínculo explícito e imutável por e-mail Google normalizado

Será persistido um vínculo único entre usuário e e-mail Google normalizado, separado do e-mail de contato/login local. Um e-mail comum que coincida com a declaração Google não vincula uma conta existente automaticamente; somente um administrador pode criar, trocar ou remover o vínculo. Para um primeiro acesso sem usuário vinculado, a API criará usuário com o e-mail Google, papel `USER`, senha local ausente e status `PENDING_APPROVAL`.

Alternativa considerada: usar o e-mail como única chave de associação. Foi descartada porque uma conta local existente não prova controle da identidade Google e poderia ser tomada por associação implícita.

### Avatar importado e controlado pelo usuário

Ao criar uma conta por Google, o backend usará a URL `picture` presente na identidade OIDC já validada para baixar uma única cópia da foto em armazenamento próprio. O download aceitará somente HTTPS para domínios Google permitidos, com limite de tamanho e validação de tipo/dimensões antes de normalizar a imagem. Falha nessa importação não bloqueia a criação pendente ou a autenticação. Depois disso, o avatar será de propriedade do usuário: uma foto enviada pelo próprio sistema substitui a cópia Google e nunca será sobrescrita por entradas futuras no Google.

O binário será servido por rota autenticada, limitada ao próprio usuário e a administradores quando necessário, e não por URL remota do Google. A atualização será um comando multipart para o usuário autenticado; o backend validará e armazenará a imagem, e o frontend a enviará somente pelo repositório Axios.

Alternativa considerada: manter a URL do Google como avatar. Foi descartada para evitar dependência de disponibilidade externa, vazamento do endereço de perfil e alteração silenciosa da imagem fora do controle do usuário.

### Sessão somente para usuário ativo

Após a autenticação local ou Google, o serviço emitirá access token e refresh cookie apenas para `ACTIVE`. Usuários `PENDING_APPROVAL` ou bloqueados receberão resposta segura sem tokens, com mensagem de que a liberação é necessária; a SPA permanecerá na entrada e mostrará esse feedback. O estado também será retornado em `GET /auth/me` e na listagem administrativa para manter a interface consistente.

Alternativa considerada: emitir JWT restrito para pendentes. Foi descartada porque todos os endpoints precisariam introduzir uma nova matriz de permissões e aumentaria o risco de acesso indevido.

### Administração por comandos específicos e auditáveis

As operações serão restritas a `ADMIN`: listagem paginada, criação, alteração de papéis, alteração de status e associação/remoção de e-mail Google. Cada alteração de credencial externa, papel ou status gera evento de auditoria sem registrar tokens, segredos ou a credencial OIDC. O cadastro administrativo aceitará senha local opcional; sua ausência impede somente a autenticação por senha.

Alternativa considerada: concentrar toda edição em um `PATCH` genérico. Foi descartada para manter validação e auditoria inequívocas para dados de identidade sensíveis.

## Risks / Trade-offs

- [Indisponibilidade ou configuração incorreta do Google] → Exibir falha segura sem criar sessão, registrar somente diagnóstico operacional sem credenciais e preservar login local.
- [Conta pendente sem retorno claro] → Retornar mensagem segura padronizada e renderizar banner persistente na tela de entrada.
- [Conflito de vínculo externo] → Aplicar unicidade no banco e tratar a colisão como `409 STATE_CONFLICT`, sem revelar dados do titular.
- [Administrador restringir a própria conta crítica] → Exigir que a alteração preserve ao menos um administrador ativo; cobrir a regra em serviço e testes.
- [Tokens Google em log ou auditoria] → Nunca persistir nem registrar a credencial; guardar somente o e-mail verificado e metadados de evento necessários.
- [URL de avatar usada para SSRF ou arquivo malformado] → Restringir origem Google, HTTPS, redirecionamentos, tamanho, MIME e decodificação; uploads passam pelas mesmas validações de conteúdo.

## Migration Plan

1. Criar migration reversível para status pendente, hash de senha anulável quando necessário, vínculo Google único e metadados de avatar; registros existentes permanecem `ACTIVE` e sem avatar.
2. Publicar a configuração do cliente Google e habilitar a validação do token no backend, mantendo as rotas de senha disponíveis.
3. Implantar API e SPA juntas; habilitar o botão Google somente quando a configuração pública estiver presente.
4. Em rollback, desabilitar a configuração/botão e rotas Google; preservar os registros pendentes e vínculos para retomar sem perda. A reversão de status será decisão administrativa, nunca automática.

## Open Questions

- Confirmar os domínios e URIs autorizados no projeto Google Cloud para desenvolvimento e produção antes da implantação.
- Confirmar se o cadastro administrativo deve exigir uma senha temporária quando não houver e-mail Google associado; a proposta permite senha opcional, mas a política operacional precisa escolher a experiência padrão.
