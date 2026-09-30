;  (interrompe job pendente ou em processamento)# API REST

Base: `/api/v1`. Respostas usam JSON, datas ISO 8601 UTC e erros no formato `{ "error": { "code", "message", "details" } }`. Listagens sao paginadas por `page` e `per_page` (maximo 100) e retornam `meta`. Rotas autenticadas usam `Authorization: Bearer <access-token>`.

## Revisão adaptativa

Todas as rotas deste grupo exigem usuário autenticado e sempre restringem dados ao próprio usuário. O contexto Review usa assuntos canônicos como conceitos; conteúdo de cards é compartilhado, mas progresso, domínio e histórico são individuais.

| Método e rota | Regra |
| --- | --- |
| `GET /review/sessions/daily` | monta ou retoma a sessão diária priorizada; aceita `limit` de 1 a 100 (padrão 20) |
| `GET /review/sessions/quick` | monta revisão rápida com os cards de maior prioridade; aceita `limit` de 1 a 100 (padrão 10) |
| `POST /review/sessions/{sessionId}/cards/{cardId}/reviews` | registra uma classificação imutável (`AGAIN`, `HARD`, `GOOD` ou `EASY`) do card pertencente à sessão do usuário |
| `GET /review/mastery-map` | retorna mapa paginado e hierárquico de domínio por assunto canônico; aceita `page` e `per_page` |
| `GET /study/notebooks/{id}/analysis` | retorna somente o estado e resultado persistido da análise assíncrona do caderno do usuário |

`GET /review/sessions/daily` e `GET /review/sessions/quick` retornam `data` com `id`, `kind` (`DAILY` ou `QUICK`), `status`, `totalCards`, `reviewedCards` e `cards`. Cada card expõe somente `id`, `front`, `back`, `concept` (`id`, `name`, `path`), `dueAt` e `priority`; a interface mantém `back` oculto até ação explícita do usuário. Não há criação de sessão aleatória nem exposição de progresso de terceiros.

`POST /review/sessions/{sessionId}/cards/{cardId}/reviews` recebe:

```json
{ "rating": "GOOD" }
```

O retorno em `data` contém `cardId`, `rating`, `reviewedAt`, `nextReviewAt`, `intervalDays`, `masteryScore` e `session`. Reenvios idênticos não podem criar dois eventos para a mesma interação; uma classificação de card fora da sessão ou de outro usuário responde `404 RESOURCE_NOT_FOUND`.

`GET /review/mastery-map` retorna uma lista paginada de nós com `concept` (`id`, `name`, `parentId`, `path`), `masteryScore` opcional de 0 a 100, `confidence` (`INSUFFICIENT`, `LOW`, `MEDIUM`, `HIGH`), `evidenceCount`, `lastEvidenceAt` e `children`. Sem amostra suficiente, `masteryScore` é `null` e a confiança é `INSUFFICIENT`; isso não representa domínio baixo.

`GET /study/notebooks/{id}/analysis` retorna `data` com `notebookId`, `algorithmVersion`, `status` (`PENDING`, `PROCESSING`, `COMPLETED`, `FAILED`), `requestedAt`, `completedAt` opcional, `summary` opcional (`correctAnswers`, `totalAnswers`, `identifiedPoints`) e `actions`. Cada ação persistida contém `type`, `concept`, `questionId` opcional, `reason`, `confidence` e `appliedAt`. Enquanto pendente ou processando, `summary` e `actions` refletem exclusivamente o resultado já persistido; a rota não inventa análise nem dispara processamento síncrono.

## Autenticacao

| Metodo e rota | Regra |
| --- | --- |
| `POST /auth/login` | autentica e cria sessao |
| `POST /auth/google` | valida a credencial OIDC Google e cria sessao somente para conta ativa vinculada |
| `POST /auth/refresh` | rotaciona refresh via cookie seguro |
| `POST /auth/logout` | revoga a sessao atual |
| `GET /auth/me` | retorna usuario e papeis |

## Catalogo e administracao

Todas as rotas abaixo que mutam dados requerem `ADMIN`. Leitura de conteudo publicado e permitida a usuario autenticado.

| Recurso | Rotas |
| --- | --- |
| Concursos | `GET, POST /exams` (itens DRAFT e REVIEW); `POST /admin/exams/with-notice` (multipart, concurso + PDF); `GET, PUT, DELETE /exams/{id}` |
| Cargos | `GET, POST /exams/{examId}/positions`; `PATCH, DELETE /positions/{id}`; `GET /positions/{id}/taxonomy-subjects`; `PUT /admin/positions/{id}/taxonomy-subjects` |
| Editais | `GET, POST /exams/{examId}/syllabi`; `POST /admin/syllabi/{id}/document` (multipart PDF); `PATCH, DELETE /syllabi/{id}` |
| Assuntos | `GET /syllabi/{id}/subjects`; `POST /subjects`; `PUT /admin/subjects/{id}/taxonomy-subjects`; `GET, PATCH, DELETE /subjects/{id}` |
| Tags | `GET, POST /tags` |
| Questoes | `GET /questions`; `POST /questions`; `GET, PATCH /questions/{id}`; `POST /questions/{id}/publish` |
| Correção de questão | `POST /questions/{id}/correction-requests`; `GET /questions/{id}/correction-requests/latest`; `POST /admin/question-correction-requests/{id}/approve` |
| Importacao | `POST /question-imports` (arquivo JSON/CSV); `GET /question-imports/{id}`; `POST /question-imports/{id}/commit`; `POST /admin/question-pdf-imports` (PDFs em lote); `GET /admin/question-pdf-imports` (histórico paginado do administrador); `GET /admin/question-pdf-imports/{id}`; `GET /admin/question-pdf-imports/{id}/analyses` |
| Revisão editorial | GET /admin/questions/drafts (ordem: REVIEW, DRAFT com gabarito, DRAFT sem gabarito, VOID); POST /admin/questions/{id}/publish |
| Taxonomia | `GET, POST, PATCH /admin/taxonomy/subjects` (lista paginada e cria assunto canônico); `POST /admin/taxonomy/aliases` (cria alias canônico) |
| Fusão de Taxonomia | `POST /admin/taxonomy/subjects/{id}/merge` recebe `{ "target_subject_id": "uuid", "reason": "texto" }`, exige ADMIN, elimina associações duplicadas, reatribui vínculos canônicos de questões e assuntos locais, e grava auditoria |
| Reconciliação de Taxonomia | `GET /admin/taxonomy/reconciliation-proposals` lista propostas determinísticas com confiança e justificativa; requer ADMIN e não executa fusões |
| Taxonomia editorial | `PUT /admin/questions/{id}/taxonomy-subjects` recebe `{ "taxonomy_subject_ids": ["uuid"] }` e substitui as associações canônicas da questão para usuários ADMIN |
| Usuários | `GET, POST /admin/users`; `PUT, DELETE /admin/users/{id}/google-identity`; `PATCH /admin/users/{id}/roles`; `PATCH /admin/users/{id}/status` |

`GET /admin/taxonomy/subjects` devolve `questionCount` por assunto canônico. O valor é agregado e inclui somente questões PUBLISHED ligadas ao nó e a todos os seus descendentes; em folhas, representa apenas as questões publicadas ligadas à própria folha.

`GET /questions` aceita filtros `syllabus_id`, `subject_id`, `tag`, `board`, `year`, `difficulty`, `content`, `status` (admin) e `origin`. `content` recebe até 200 caracteres e localiza o texto no enunciado ou em qualquer alternativa publicada. Cada questão retornada inclui `qualityNotice` quando a análise editorial recomendou revisão; é um resumo seguro e não informa alternativa ou gabarito. Também inclui `answerKeySource`, que é `OFFICIAL` quando o gabarito foi explicitamente recuperado da fonte e `AI_ESTIMATED` quando foi inferido pela IA; o cliente deve sinalizar visualmente a segunda hipótese. A importacao primeiro valida e cria relatorio; `commit` insere apenas linhas validas explicitamente aprovadas. Assim nao ha insercao silenciosa.
### Correção assistida de questão

`POST /questions/{id}/correction-requests` recebe `{ "instruction": "texto entre 3 e 2000 caracteres" }`, exige usuário autenticado e cria uma solicitação para **uma única questão publicada**. Quando houver PDF de origem, o worker divide o enunciado do snapshot por linhas, remove espaços e caracteres especiais apenas das extremidades e pesquisa a primeira linha útil de maior comprimento; alternativas não participam da busca automática. As páginas encontradas, com duas páginas vizinhas, são unidas às evidências visuais. Quando a instrução contém um comando de busca, como `procure por trecho "..."`, `pesquise pela expressão ...` ou equivalente, o worker também pesquisa o trecho no PDF de origem e incorpora a mesma janela de evidência. Se a instrução mencionar `gabarito`, `resposta correta` ou `alternativa correta`, o worker também anexa, no máximo, 12 páginas que contenham “gabarito” no PDF para verificação oficial. A resposta retorna a solicitação em `PENDING`; não altera a questão antes da aprovação administrativa.

`GET /questions/{id}/correction-requests/latest` retorna a última solicitação visível ao solicitante. Administradores recebem também a proposta estruturada quando o worker terminar; outros usuários recebem apenas o estado. Estados possíveis: `PENDING`, `PROCESSING`, `PROPOSED`, `APPROVED`, `REJECTED` e `FAILED`.

`POST /admin/question-correction-requests/{id}/approve` exige `ADMIN` e aplica exclusivamente a proposta já validada para a mesma questão. A proposta informa uma lista ordenada `figures`, com páginas restritas à janela de evidência do PDF original e marcadores `[[FIGURA:1]]`, `[[FIGURA:2]]` no enunciado. A aprovação promove uma prévia extraída para cada figura como ativo autenticado, atualiza `source_pdf_pages` para as páginas visuais aprovadas e valida a página contra a lista de evidências efetivamente usada pelo worker; caminhos internos nunca são persistidos no enunciado. A proposta também declara os metadados visíveis `board`, `exam`, `position` e `year`: banca e ano atualizam os campos próprios; concurso e cargo são preservados na origem textual. Valores não impressos no PDF são `null` e nunca são inferidos. O cabeçalho que apenas repete esses metadados é removido do início do enunciado. As alternativas podem receber somente correções estruturais ou de formatação provenientes da extração. O `correct_option_id` só pode mudar quando a proposta indicar uma página de evidência que apresente explicitamente o gabarito oficial; a aprovação então atualiza o gabarito com fonte `OFFICIAL`. `GET /admin/question-correction-requests/{id}/asset-preview/{index}` entrega, somente a administradores e enquanto a solicitação estiver `PROPOSED`, a prévia de cada figura; o índice começa em 1. A aprovação preserva quantidade, identificadores e ordem das alternativas. A solicitação mantém os instantâneos anterior e proposto para auditoria. O worker não publica alterações automaticamente.


### Logos automáticos de concursos

As respostas de concurso incluem `institutionLogoUrl` e `organizerLogoUrl`, ambas anuláveis. Em `POST /exams` e `POST /admin/exams/with-notice`, o serviço consulta o Gemini com Google Search Grounding para identificar somente domínios institucionais oficiais e converte o domínio validado em ícone. A consulta não bloqueia o cadastro: em caso de indisponibilidade, ausência de fonte confiável ou cota excedida, os dois campos retornam `null`. Nenhum URL de logo é aceito do cliente.

### Documento do edital

`POST /api/v1/admin/exams/with-notice` requer `ADMIN` e recebe `multipart/form-data` com `document` opcional e os metadados `name`, `organizer` opcional e `year` opcional; clientes web os enviam como parâmetros da requisição para compatibilidade com o parser multipart. Em uma única transação cria o concurso e seu edital principal; quando há PDF válido, persiste o arquivo, agenda a extração e devolve `{ "exam", "syllabus", "processingJob" }`.

`POST /api/v1/admin/syllabi/{id}/document` requer `ADMIN` e recebe `multipart/form-data` com o campo `document`. O arquivo deve declarar `application/pdf` e iniciar com a assinatura `%PDF-`. O serviço calcula SHA-256, mantém o conteúdo em armazenamento local idempotente e atualiza o edital existente sem criar um novo registro. Na mesma transação, cria um job PENDING para o hash enviado, salvo quando já existir um job pendente, em processamento ou concluído para o mesmo documento.

A resposta segue o envelope padrão e devolve `{ "id": "uuid", "document_sha256": "..." }`. A listagem de editais inclui `documentSha256`, `documentOriginalName`, `documentMimeType` e `documentSize`; não expõe o caminho interno do arquivo.

### Processamento de edital

`POST /api/v1/admin/syllabi/{id}/processing-jobs` requer `ADMIN` e recebe opcionalmente `{ "reprocess": false }`. O edital precisa possuir um PDF preservado. A operação cria, ou reutiliza enquanto pendente/em processamento, um job persistido e devolve `{ "id", "syllabusId", "documentSha256", "status", "progress", "errorMessage", "createdAt", "startedAt", "finishedAt" }` no envelope padrão. `reprocess=true` agenda nova execução explícita para o mesmo PDF; a deduplicação dos resultados é responsabilidade do worker.

`GET /api/v1/admin/syllabi/{id}/processing-jobs/latest` requer `ADMIN` e retorna o último job do edital ou `404 RESOURCE_NOT_FOUND`. Estados possíveis: `PENDING`, `PROCESSING`, `COMPLETED` e `FAILED`. O campo `progress` varia de 0 a 100 e `errorMessage` só contém mensagem segura para administração.

`GET /api/v1/admin/syllabi/{id}/extractions` requer `ADMIN` e devolve as páginas extraídas, ordenadas por página, com `{ "pageNumber", "textContent", "startOffset", "endOffset", "documentSha256" }`. Ao concluir a extração, o worker envia somente trechos do edital ao provider configurado e cria cargos, assuntos locais vinculados ao edital e associações à taxonomia canônica. A leitura separa Anexo I (cargos) do Anexo IV (conteúdos) e normaliza uma taxonomia semântica em áreas, matérias, assuntos e subassuntos, conforme evidência do edital; áreas recorrentes como Direito, Tecnologia da Informação e Língua Portuguesa agrupam suas disciplinas quando aplicável. Numerações e identificadores editoriais não integram os nomes. Antes de criar um nó canônico, o worker procura equivalência ativa exata e por prefixo de assunto para reutilizá-lo; cada assunto preserva a página apontada pelo modelo. O endpoint permanece somente leitura para revisão administrativa.



### Preview de importacao

`POST /api/v1/question-imports` requer `ADMIN` e recebe:

```json
{ "format": "JSON", "content": "[...]" }
```

Cada linha pode declarar opcionalmente `source`, `reference_url` e `origin` (`EXAM`, `ORIGINAL` ou `AI`). Essas informações são preservadas no rascunho criado na confirmação.
ou `{ "format": "CSV", "content": "statement,options,correct_option\\n..." }`.
A resposta contem `validRows`, `invalidRows` e `rows` com o numero da linha, a situacao e os erros. Uma linha repetida ou cujo enunciado normalizado já exista no banco recebe `DUPLICATE_CANDIDATE`, permanece no relatório persistido e não é criada na confirmação. O preview persiste o relatorio e cada linha, associado ao administrador que o enviou, mas nao cria questoes. A confirmacao posterior podera gravar somente linhas validas explicitamente aprovadas.
`POST /api/v1/question-imports/{id}/commit` requer `ADMIN` e recebe `{ "syllabus_id": "uuid" }`. A operacao cria apenas as linhas validas como rascunhos, preserva as invalidas no relatorio e so pode ser executada uma vez.
## Cadernos, simulados e resolucao

| Metodo e rota | Regra |
| --- | --- |
| `POST /notebooks` | cria caderno e congela selecao de questoes |
| `GET /notebooks` | lista cadernos do usuario atual |
| `GET /notebooks/{id}` | detalhes e progresso, com isolamento por dono |
| `POST /notebooks/{id}/start` | inicia ou retoma a execução e grava `startedAt` |
| `POST /notebooks/{id}/pause` | pausa o contador e preserva a duração acumulada |
| `PATCH /notebooks/{id}/active-question` | persiste a questão ativa da seleção congelada |
| `GET /notebooks/{id}/statistics` | resumo do progresso e desempenho do caderno |
| `POST /notebooks/{id}/questions/{questionId}/attempts` | inicia tentativa |
| `POST /attempts/{id}/answers` | registra marcacao imutavel |
| `POST /attempts/{id}/complete` | conclui tentativa e aplica correcao conforme modo |
| `POST /notebooks/{id}/finish` | finaliza caderno/simulado e revela resultado quando cabivel |

`POST /notebooks/{id}/start` é idempotente enquanto o caderno está em andamento e retorna o caderno com `status`, `startedAt`, `finishedAt` e `durationSeconds`. `POST /notebooks/{id}/pause` acumula a duração já decorrida e muda o estado para `PAUSED`; `start` retoma a contagem sem perder o acumulado. `GET /notebooks/{id}/statistics` retorna `total`, `answered`, `correct`, `incorrect`, `percentage`, `averageElapsedSeconds`, `elapsedSeconds` e `answeredQuestionIds`, sempre restritos ao proprietário. `POST /notebooks/{id}/finish` encerra o caderno, calcula a duração acumulada e impede novas tentativas. Um caderno finalizado não pode ser iniciado nem finalizado novamente.
`PATCH /notebooks/{id}/active-question` recebe `{ "question_id": "uuid" }`, exige que a questão pertença à seleção congelada e atualiza somente o caderno do proprietário. `GET /notebooks/{id}` devolve `activeQuestionId`; a interface o usa para restaurar a questão aberta após pausa, fechamento ou reabertura.


`POST /notebooks` recebe `name`, `mode` (`STUDY` ou `EXAM`), `quantity` e o objeto opcional `filters`. Para estudo direcionado, `filters` deve informar conjuntamente `exam_id`, `position_id` e uma lista não vazia `subject_ids`; o cargo precisa pertencer ao concurso e todo assunto deve estar associado ao cargo. Os filtros aceitos no MVP sao `subject_id`, `board`, `year` e `difficulty` (`EASY`, `MEDIUM` ou `HARD`). O cliente nao envia IDs de questoes: o service consulta somente questoes publicadas, persiste a lista retornada em `notebook_questions` e a composicao nunca muda. A API responde `422 VALIDATION_FAILED` quando os filtros nao encontram a quantidade solicitada, em vez de completar o caderno com questoes fora dos filtros.
A criação exclui questões com resposta final do mesmo usuário nos últimos 30 dias e questões já congeladas em cadernos `DRAFT`, `IN_PROGRESS` ou `PAUSED` do mesmo usuário. Se as exclusões não deixarem a quantidade solicitada, responde `422 VALIDATION_FAILED`; não reutiliza questões silenciosamente. A seleção é ordenada de forma determinística por assunto canônico antes de ser persistida. `POST /notebooks/{id}/questions/{questionId}/attempts` e `POST /attempts/{id}/answers` aceitam somente cadernos `IN_PROGRESS`; para `PAUSED`, retornam `409 STATE_CONFLICT`.


## Desempenho e revisoes

| Metodo e rota | Regra |
| --- | --- |
| `GET /dashboard/me` | dashboard por edital, com agregação hierárquica e amostra de dados |
| `GET /statistics/me` | totais e metricas gerais |
| `GET /statistics/me/subjects` | desempenho por assunto, com amostra e confianca |
| `GET /statistics/me/evolution` | serie temporal, com agrupamento configuravel |
| `GET /recommendations/me` | 3-5 prioridades explicaveis |
| `GET /reviews/me` | fila do usuario, filtravel por vencidas |
| `POST /reviews/{id}/answer` | registra resultado de revisao e reagenda |
| `POST /questions/{id}/review` | marca/desmarca revisao manual |

## Assistente (Fase 3)

| Metodo e rota | Regra |
| --- | --- |
## Assistente com RAG de editais

| Método e rota | Regra |
| --- | --- |
| `GET /assistant/conversations` | lista somente as conversas do usuário autenticado |
| `GET /assistant/syllabi` | lista editais com páginas extraídas disponíveis para consulta |
| `POST /assistant/conversations` | cria conversa com `syllabus_id` e título opcional; o edital precisa ter páginas extraídas |
| `POST /assistant/conversations/{id}/messages` | recebe `{ "content": "pergunta" }`, recupera páginas do edital e grava pergunta, resposta, provider, modelo e evidências |

As respostas são produzidas por um provider desacoplado. A versão local determinística só resume evidências recuperadas, não inventa fontes e devolve as páginas utilizadas. Cada mensagem é imutável e sempre permanece restrita ao dono da conversa. Não há endpoint de geração editorial nesta entrega.

### Configuração de provider de IA

`AI_PROVIDER` seleciona o provider do assistente: `gemini`, `openai` ou `local` (fallback determinístico). Para Gemini, defina `GEMINI_API_KEY` exclusivamente no ambiente do servidor e escolha o modelo em `GEMINI_MODEL` (padrão `gemini-2.5-flash`). Para OpenAI, use `OPENAI_API_KEY` e `OPENAI_MODEL` (padrão `gpt-5`). Nenhuma chave é exposta pela API ou frontend. Ambos os providers recebem somente pergunta e trechos recuperados do edital; no Gemini, a chave segue exclusivamente no cabeçalho HTTPS da chamada ao endpoint `generateContent`.

## Descoberta web controlada

| Método e rota | Regra |
| --- | --- |
| `GET /admin/discovery/resources` | lista candidatos de provas e gabaritos, com proveniência; requer ADMIN |
| `POST /admin/discovery/searches` | recebe `{ "query": "...", "resource_type": "EXAM" }`, consulta somente o provider configurado e persiste no máximo 10 candidatos; requer ADMIN |

O provider usa `DISCOVERY_PROVIDER_BASE_URL` e só aceita resposta JSON de hosts presentes em `DISCOVERY_ALLOWED_HOSTS`. A API limita consultas a 10 resultados por requisição, não segue URLs de candidatos, não baixa arquivos e registra provider, URL de origem, consulta e data de descoberta. Tipos válidos: `EXAM` e `ANSWER_KEY`.
## Codigos importantes

`400` entrada invalida, `401` autenticacao ausente/expirada, `403` papel ou propriedade insuficiente, `404` recurso inexistente ou invisivel, `409` estado incompatível (por exemplo, concluir duas vezes), `422` validacao de dominio, `429` rate limit e `500` erro inesperado com request id.

## Contratos de autenticacao

`POST /auth/login` recebe `{ "email": "user@example.com", "password": "...", "device_name": "opcional" }`. Em sucesso, devolve `data` com `access_token` e `user`; o refresh token opaco e entregue exclusivamente em cookie `HttpOnly`, `Secure` e `SameSite=Lax`.

`POST /auth/google` recebe `{ "credential": "oidc-id-token" }`. A credencial é validada no servidor quanto à assinatura, emissor Google, público configurado, expiração e `email_verified`; nunca é persistida, registrada em logs ou devolvida. Para uma identidade Google explicitamente vinculada a usuário `ACTIVE`, a resposta é igual ao login local: `data` contém `access_token` e `user`, e o refresh token é entregue somente no cookie seguro.

Quando a identidade Google válida ainda não possuir vínculo, a API cria uma única conta com `status: "PENDING_APPROVAL"`, papel `USER`, sem senha local e com o e-mail Google normalizado. A resposta é `200` sem cookies de refresh e contém somente `{ "status": "PENDING_APPROVAL", "message": "Seu acesso aguarda liberação administrativa." }` em `data`; ela nunca contém `access_token`. Nova tentativa da mesma identidade retorna a mesma resposta. Se o e-mail Google coincidir com uma conta local sem vínculo explícito, a API devolve `409 STATE_CONFLICT`; credencial inválida devolve `401 UNAUTHENTICATED` sem criar ou alterar registros.

`POST /auth/refresh` e `POST /auth/logout` usam somente o refresh token do cookie. O refresh cria uma nova sessao na mesma familia, revoga o token anterior e revoga toda a familia se um token ja rotacionado for reutilizado.

`GET /auth/me` devolve `{ "id", "email", "name", "roles", "status" }` em `data`. Credenciais, token expirado ou revogado e usuario bloqueado retornam `401 UNAUTHENTICATED`, sem revelar qual condicao falhou. Login local com credenciais corretas de usuário `PENDING_APPROVAL` não cria sessão e devolve o mesmo retorno seguro de aprovação pendente de `POST /auth/google`.

### Foto de perfil

`GET /profile/avatar/metadata` retorna disponibilidade, MIME e data de atualização da foto do próprio usuário. `GET /profile/avatar` entrega o binário privado somente ao usuário autenticado; sem foto retorna `404 RESOURCE_NOT_FOUND`. `POST /profile/avatar` recebe multipart com o campo `avatar`, valida conteúdo, tamanho máximo de 5 MB e dimensões entre 32 e 4096 pixels, normaliza para WebP e substitui a foto anterior. Caminhos internos nunca são expostos; falhas de validação retornam `422 VALIDATION_FAILED`.

### Administração de usuários

Todas as rotas desta seção exigem papel `ADMIN`. `GET /admin/users` aceita `page`, `per_page` (padrão 25, máximo 100), `query` e `status`. Devolve lista paginada de `{ "id", "name", "email", "googleEmail", "roles", "status" }`, sem hash de senha, tokens ou eventos de auditoria.

`POST /admin/users` recebe `{ "name": "...", "email": "...", "roles": ["USER"], "status": "ACTIVE", "password": "opcional", "google_email": "opcional" }`. A senha local é opcional; e-mails local e Google são normalizados e únicos nos respectivos vínculos. Conflitos devolvem `409 STATE_CONFLICT` e validações devolvem `422 VALIDATION_FAILED`, sem criar usuário parcial.

`PUT /admin/users/{id}/google-identity` recebe `{ "google_email": "verified@example.com" }` e cria ou substitui o vínculo explícito. `DELETE /admin/users/{id}/google-identity` remove-o. Ambos preservam a unicidade e gravam auditoria segura. `PATCH /admin/users/{id}/roles` recebe `{ "roles": ["USER", "ADMIN"] }`; `PATCH /admin/users/{id}/status` recebe `{ "status": "ACTIVE|PENDING_APPROVAL|BLOCKED" }`. Todas as alterações são transacionais e auditadas; uma operação que removeria ou bloquearia o último administrador ativo devolve `409 STATE_CONFLICT` e mantém o estado anterior.

Os services recebem Request DTOs e retornam Response DTOs; JWT, cookies, Argon2id e Doctrine pertencem aos adaptadores de infraestrutura.

### Navegação de questões congeladas

GET /api/v1/notebooks/{id}/questions?page=1&per_page=25 requer autenticação e devolve uma lista paginada de questões na ordem gravada em notebook_questions. A rota filtra o caderno pelo proprietário, preserva a seleção congelada e não repete os filtros usados na criação. Um caderno inexistente ou de outro usuário retorna 404 RESOURCE_NOT_FOUND.

### Proveniência de conteúdo programático

`POST /api/v1/subjects` aceita opcionalmente `source_excerpt`, `source_page`, `source_start_offset` e `source_end_offset` junto a `syllabus_id`, `name`, `parent_id` e `sort_order`. Esses campos registram a evidência do conteúdo dentro do PDF do edital e são retornados por `GET /api/v1/syllabi/{syllabusId}/subjects`. `source_page` começa em 1; offsets começam em 0 e o fim não pode anteceder o início.

### Associação canônica de assuntos por cargo

`GET /api/v1/positions/{id}/taxonomy-subjects` requer autenticação e retorna `{ positionId, taxonomySubjectIds }`. `PUT /api/v1/admin/positions/{id}/taxonomy-subjects` requer `ADMIN` e recebe `{ "taxonomy_subject_ids": ["uuid"] }`. A operação substitui a matriz canônica daquele cargo em uma transação; aceita assunto ativo e permite que o mesmo assunto seja associado a vários cargos.

`GET /api/v1/study-positions/{positionId}/subjects` requer autenticação e retorna somente os assuntos ativos associados ao cargo, com `id`, `parentId`, `name` e `level`.

### Associação canônica de assuntos do edital

`PUT /api/v1/admin/subjects/{id}/taxonomy-subjects` requer `ADMIN` e recebe `{ "taxonomy_subject_ids": ["uuid"] }`. A operação substitui, em transação, somente as associações canônicas do assunto local informado. Cada assunto canônico precisa existir e estar ativo. Ela não cria taxonomia, não executa fusões e não modifica associações de questões. A resposta devolve `subjectId` e `taxonomySubjectIds` no envelope padrão.

### Dashboard por concurso e edital

`GET /api/v1/dashboard/me?syllabus_id={uuid}` ou `GET /api/v1/dashboard/me?exam_id={uuid}` requer autenticação. Com `exam_id`, inclui apenas tentativas cujas questões globais tenham assunto canônico associado a pelo menos um cargo daquele concurso. Sem `syllabus_id`, seleciona o primeiro edital no qual o usuário tenha tentativas concluídas. A resposta retorna os editais disponíveis, totais únicos de respostas finais e métricas por assunto canônico. Cada resposta classificada contribui também para seus ancestrais; `sufficientData` só é verdadeiro com pelo menos 10 respostas em 3 dias distintos.

### Plano de estudos inteligente

`GET /api/v1/study-plan/me` requer autenticação e retorna um plano calculado a partir de tentativas finais do usuário. Cada prioridade contém o assunto, a taxa de acerto, tamanho da amostra, dias distintos, uma explicação observável e uma ação sugerida. Uma prioridade só recebe classificação de fragilidade quando tem ao menos 10 respostas em 3 dias distintos; dados menores são apresentados como insuficientes. O plano devolve de 0 a 5 prioridades e nunca cria cadernos automaticamente.

### Meta semanal de estudo

`GET /api/v1/study-goals/me` retorna a meta semanal do usuário autenticado, com alvo de respostas concluídas, progresso na janela atual de sete dias e percentual. `PUT /api/v1/study-goals/me` recebe `{ "weekly_question_goal": 20 }`, exige inteiro entre 1 e 500 e cria ou atualiza exclusivamente a meta do próprio usuário.


### Importação assíncrona de PDFs de questões

`POST /api/v1/admin/question-pdf-imports` requer ADMIN e recebe `multipart/form-data` com `syllabus_id` e um ou mais `documents[]` em PDF. Cada arquivo gera um job isolado e devolve uma lista com `id`, status, progresso, páginas candidatas, questões extraídas, criadas, duplicadas, com erro, classificadas e assuntos canônicos criados.

`GET /api/v1/admin/question-pdf-imports/{id}` permite ao administrador que criou o job acompanhar a mesma estrutura. Estados: `PENDING`, `PROCESSING`, `COMPLETED`, `FAILED` e `CANCELLED`. O worker transmite somente páginas candidatas a questão e a lista de taxonomia ao provider de IA autorizado; o PDF original não é transmitido integralmente. Cada candidato possui checkpoint persistido (`PENDING`, `PROCESSING`, `COMPLETED` ou `FAILED`), com retentativa limitada e recuperação de lease; o resumo do job é consolidado a partir desses estados. Questões são criadas como `REVIEW`, sem gabarito inventado.
`GET /api/v1/admin/question-pdf-imports/{id}/analyses` requer ADMIN e só retorna análises do job criado pelo próprio administrador. Cada item contém provider/modelo, metadados com páginas de evidência, avaliação estrutural, avaliação de gabarito e imagem, duração, tokens/custo reportados ou `UNAVAILABLE`, achados de resumo seguro e âncoras visuais. O endpoint nunca devolve alternativa correta inferida nem conteúdo que revele o gabarito. O resumo do job (`GET .../{id}` e a listagem) inclui `analysisTelemetry` com agregados equivalentes; pode ser `null` para jobs legados sem análise.



### Ativos visuais de questões

`GET /api/v1/question-assets/{id}` requer autenticação e entrega uma figura extraída do PDF. Figuras de questões `PUBLISHED` ficam disponíveis a qualquer usuário autenticado; ativos de itens em `REVIEW` ou `DRAFT` são restritos a ADMIN. O cliente deve buscá-los autenticadamente, sem URL pública do arquivo.

### Seleção global por edital

As questões são globais e classificadas por taxonomia canônica; não pertencem a concurso ou edital. Ao criar um caderno, o filtro opcional `syllabus_id` restringe a seleção a questões cujos assuntos canônicos constem no edital informado. Banca, concurso de origem e ano são apenas metadados de cada questão.

### Histórico de importações de PDFs

`GET /api/v1/admin/question-pdf-imports?page=1&per_page=25` requer `ADMIN` e retorna somente os jobs criados pelo administrador autenticado, em ordem decrescente de criação. A resposta é paginada e cada item possui o mesmo resumo do job individual. Isso permite retomar o acompanhamento após sair da tela, sem expor documentos de outro administrador.

### Proveniência de questões importadas

Questões criadas pela importação de PDF preservam internamente o `source_pdf_job_id` e as páginas (`source_pdf_pages`) de onde foram extraídas. O PDF de origem é imutável; após uma aprovação editorial de figura, `source_pdf_pages` pode ser refinado para a página visual comprovada, mantendo a evidência mais precisa para usos posteriores.

| Dúvida com fonte da questão | `POST /questions/{id}/pdf-assistance` recebe `{ "question": "..." }`, exige autenticação e responde com conteúdo da IA, provider/modelo e páginas de evidência; usa exclusivamente o PDF e as páginas associados à questão importada. |


### Fila editorial

GET /api/v1/admin/questions/drafts requer ADMIN, é paginado e retorna os estados editoriais nesta ordem: primeiro REVIEW (revisar), depois DRAFT com gabarito, em seguida DRAFT sem gabarito e por último VOID (inválidas ou duplicadas). Itens VOID são somente informativos e não podem ser aprovados, classificados ou publicados.

### Aprovação editorial em lote

POST /api/v1/admin/questions/publish-batch requer ADMIN e recebe { question_ids: [uuid] }. Publica somente questões em DRAFT que já tenham gabarito válido; a resposta devolve STATE_CONFLICT quando nenhuma questão selecionada é elegível.

### Relatório de auditoria de questões

`GET /api/v1/admin/question-audits/latest` requer `ADMIN` e devolve no envelope padrão a última execução persistida, com `id`, `algorithmVersion`, `scope`, `status`, `summary`, datas e eventual `errorMessage`. O endpoint é estritamente somente leitura: não corrige, publica, reimporta ou modifica questões. Quando ainda não há execução, retorna `404 RESOURCE_NOT_FOUND`.

`GET /api/v1/admin/question-audits/latest/findings?page=1&per_page=25` requer `ADMIN`, aceita no máximo 100 itens por página e devolve os achados da última auditoria na paginação padrão. Cada item contém `questionId`, `sourcePdfJobId`, `sourcePage`, `code`, `confidence`, `status`, `message`, `createdAt` e, quando houver proposta estrutural não mutante, `structureAfter` (por exemplo, `rendering: CODE_BLOCK` ou `MATCHING_COLUMNS`). É uma rota estritamente somente leitura; ausência de auditoria retorna `404 RESOURCE_NOT_FOUND`.

### Notificações de correção em tempo real

`GET /question-correction-requests/latest` retorna, para o usuário autenticado, o último resultado concluído (`PROPOSED` ou `FAILED`) de correção solicitado por ele. A proposta detalhada só é incluída para administradores. O endpoint serve como recuperação após reconexão do WebSocket.

### Configuração Google

Defina `GOOGLE_OIDC_CLIENT_ID` na API e o mesmo identificador público em `VITE_GOOGLE_CLIENT_ID` na SPA. O cliente OAuth deve ser do tipo Web e autorizar exclusivamente as origens HTTPS/publicadas e os endereços de desenvolvimento confirmados para este produto. Não use nem exponha um client secret: o fluxo usa a credencial OIDC do Google Identity Services, verificada pela API.


### Arena: Duelo privado

Todas as rotas Arena exigem autenticação. `POST /api/v1/arena/duels` cria uma sala com `{ "max_players": 2..8, "subjects_per_player": 1..5, "question_count": 5|10|15|20, "question_seconds": 15|30|60 }`; a resposta devolve estado sanitizado e código curto. `POST /api/v1/arena/duels/join` recebe `{ "code": "ABC123" }`; `GET /api/v1/arena/duels/{id}` recupera estado somente para participante.

`PUT /api/v1/arena/duels/{id}/subjects` recebe exatamente `subjects_per_player` IDs canônicos distintos e marca o participante pronto. O servidor congela questões `PUBLISHED` equilibradas entre assuntos quando todos estiverem prontos. `POST /api/v1/arena/duels/{id}/start` inicia somente pelo criador.

`POST /api/v1/arena/duels/{id}/answers` recebe `{ "option_id": "uuid" }` e aceita somente a primeira resposta, antes do prazo do servidor. Duplicidade, atraso ou alternativa inválida retornam `409 STATE_CONFLICT`. Por `received_at, id`, acertos recebem 100, 75, 50 e 25; erros, -25. O estado aberto oculta gabarito, escolhas adversárias e questões futuras.

Socket.IO usa a sala autenticada `arena:duel:{id}`. `arena:duel-updated` contém `{ duelId }`; após evento ou reconexão, o cliente recupera `GET /arena/duels/{id}` como fonte autoritativa.


`POST /api/v1/arena/duels/{id}/round/close` requer participante autenticado. Fecha a rodada somente quando todos responderem ou quando o prazo do servidor tiver expirado; a operação é idempotente, calcula pontuação e materializa as tentativas de desempenho `ARENA_DUELO`.

### Arena: salas públicas e privadas

`POST /arena/duels` recebe `max_players`, `subjects_per_player`, `question_count`, `question_seconds` e `visibility` (`PUBLIC` ou `PRIVATE`, padrão `PRIVATE`). `POST /arena/duels/join` mantém a entrada por `{ "code": "..." }` exclusivamente para salas privadas. `POST /arena/duels/{id}/join` permite ao usuário autenticado entrar em sala pública `WAITING` sem código.

`GET /arena/duels/public?page=1&per_page=25` lista somente salas públicas `WAITING`; `GET /arena/duels/mine/private?page=1&per_page=25` lista somente salas privadas `WAITING` criadas pelo usuário autenticado. Ambos devolvem paginação padrão e resumos sem código ou participantes. `GET /arena/subjects?page=1&per_page=25` devolve assuntos canônicos ativos para a escolha individual de prontidão. Detalhes e comandos de um duelo continuam restritos a seus participantes; tentativa de acessar sala privada de terceiro retorna `404`.

- `DELETE /arena/duels/{id}` remove uma sala `WAITING` apenas quando solicitado pelo criador; salas iniciadas, finalizadas ou de terceiros retornam `409 STATE_CONFLICT`.
- A listagem pública inclui `creatorUserId` apenas para permitir ao cliente autenticado exibir a ação de remoção da própria sala aguardando; `DELETE /arena/duels/{id}` mantém a regra de propriedade e estado `WAITING`.

### Interações de aprendizagem por questão

Todas as rotas deste grupo exigem autenticação, operam exclusivamente sobre questões `PUBLISHED` e seguem o envelope padrão. O estado pessoal é limitado ao usuário autenticado; comentários são públicos para usuários autenticados e relatos ficam visíveis somente ao autor e à administração.

| Rota | Contrato |
| --- | --- |
| `GET /questions/{id}/learning-interactions` | devolve as flags pessoais `favorite`, `reviewLater`, `notMastered`, a anotação privada opcional e os contadores públicos de comentários e relatos próprios |
| `PATCH /questions/{id}/learning-interactions` | recebe uma ou mais flags booleanas `favorite`, `review_later` e `not_mastered`; flags ausentes são preservadas |
| `PUT /questions/{id}/note` | cria ou atualiza a anotação privada com `{ "content": "1 a 5000 caracteres" }` |
| `DELETE /questions/{id}/note` | remove exclusivamente a anotação do próprio usuário; ausência é idempotente |
| `GET /questions/{id}/comments` | lista paginada comentários públicos, com `parentId` opcional e autor sanitizado |
| `POST /questions/{id}/comments` | recebe `{ "content": "1 a 2000 caracteres", "parent_id": "uuid opcional" }`; o pai, quando presente, deve pertencer à mesma questão |
| `POST /questions/{id}/problem-reports` | recebe `{ "category": "CONTENT|ANSWER_KEY|IMAGE|DUPLICATE|OTHER", "description": "3 a 2000 caracteres" }` e cria relato `OPEN` auditável |
| `POST /questions/{id}/explanations` | recebe `{ "attempt_id": "uuid opcional" }`, cria ou reutiliza execução de explicação do próprio usuário |
| `GET /questions/{id}/explanations/latest` | retorna a execução mais recente visível ao solicitante, ou `404 RESOURCE_NOT_FOUND` se não houver |

`GET /questions/{id}/learning-interactions` retorna `data` com `questionId`, `favorite`, `reviewLater`, `notMastered`, `note` opcional (`content`, `createdAt`, `updatedAt`), `commentCount` e `ownOpenReportCount`. A atualização de flags é atômica por usuário e questão e não modifica tentativas, respostas ou o resultado corrigido. Ativar ou desativar `not_mastered` registra um sinal de aprendizagem imutável distinto.

Cada comentário retornado contém `id`, `content`, `parentId` opcional, `author` (`id`, `name`) e `createdAt`; não expõe e-mail ou dados privados. Relatos retornam ao autor `id`, `category`, `description`, `status` (`OPEN`, `TRIAGED`, `RESOLVED`, `REJECTED`), `createdAt` e `updatedAt`; não há alteração de status por usuário comum.

A explicação retorna `id`, `questionId`, `attemptId` opcional, `status` (`PENDING`, `PROCESSING`, `COMPLETED`, `FAILED`), `requestedAt`, `completedAt` opcional e, somente em `COMPLETED`, `result` com `summary`, `concepts`, `reinforcement` e `safetyMode`. Antes de uma tentativa concluída, `safetyMode` é `CONCEPTUAL_ONLY`: o resultado não pode conter gabarito, alternativa correta nem texto que permita inferi-los. Após tentativa concluída do próprio usuário, `safetyMode` é `POST_ANSWER`; a resposta pode relacionar escolha, resultado e conceitos. Execuções pendentes, em processamento ou concluídas com o mesmo usuário, questão, tentativa e versão do algoritmo são reutilizadas. Falhas expõem somente mensagem segura.

`GET /questions` passa a aceitar, para o usuário autenticado, os filtros combináveis `answered` (`true|false`), `favorite`, `review_later`, `not_mastered`, `has_note` (`true|false`) e `taxonomy_subject_id`. Eles são combinados com os filtros já existentes e conservam a paginação padrão; filtros pessoais nunca consideram dados de outro usuário.

### Histórico de importação PDF

Cada item de `GET /admin/question-pdf-imports` e `GET /admin/question-pdf-imports/{id}` inclui `createdAt`, `startedAt` e `finishedAt`. `startedAt` informa o início efetivo da execução; `finishedAt` é preenchido em conclusão, falha ou cancelamento. O histórico administrativo apresenta os três horários e mantém `finishedAt` como pendente enquanto o job estiver ativo.
