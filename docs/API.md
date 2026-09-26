;  (interrompe job pendente ou em processamento)# API REST

Base: `/api/v1`. Respostas usam JSON, datas ISO 8601 UTC e erros no formato `{ "error": { "code", "message", "details" } }`. Listagens sao paginadas por `page` e `per_page` (maximo 100) e retornam `meta`. Rotas autenticadas usam `Authorization: Bearer <access-token>`.

## Autenticacao

| Metodo e rota | Regra |
| --- | --- |
| `POST /auth/login` | autentica e cria sessao |
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
| Importacao | `POST /question-imports` (arquivo JSON/CSV); `GET /question-imports/{id}`; `POST /question-imports/{id}/commit`; `POST /admin/question-pdf-imports` (PDFs em lote); `GET /admin/question-pdf-imports` (histórico paginado do administrador); `GET /admin/question-pdf-imports/{id}` |
| Revisão editorial | `GET /admin/questions/drafts`; `POST /admin/questions/{id}/publish` |
| Taxonomia | `GET, POST, PATCH /admin/taxonomy/subjects` (lista paginada e cria assunto canônico); `POST /admin/taxonomy/aliases` (cria alias canônico) |
| Fusão de Taxonomia | `POST /admin/taxonomy/subjects/{id}/merge` recebe `{ "target_subject_id": "uuid", "reason": "texto" }`, exige ADMIN, elimina associações duplicadas, reatribui vínculos canônicos de questões e assuntos locais, e grava auditoria |
| Reconciliação de Taxonomia | `GET /admin/taxonomy/reconciliation-proposals` lista propostas determinísticas com confiança e justificativa; requer ADMIN e não executa fusões |
| Taxonomia editorial | `PUT /admin/questions/{id}/taxonomy-subjects` recebe `{ "taxonomy_subject_ids": ["uuid"] }` e substitui as associações canônicas da questão para usuários ADMIN |
| Usuarios | `GET /users`; `PATCH /users/{id}/roles`; `PATCH /users/{id}/status` |

`GET /questions` aceita filtros `syllabus_id`, `subject_id`, `tag`, `board`, `year`, `difficulty`, `status` (admin) e `origin`. A importacao primeiro valida e cria relatorio; `commit` insere apenas linhas validas explicitamente aprovadas. Assim nao ha insercao silenciosa.

### Logos automáticos de concursos

As respostas de concurso incluem `institutionLogoUrl` e `organizerLogoUrl`, ambas anuláveis. Em `POST /exams` e `POST /admin/exams/with-notice`, o serviço consulta o Gemini com Google Search Grounding para identificar somente domínios institucionais oficiais e converte o domínio validado em ícone. A consulta não bloqueia o cadastro: em caso de indisponibilidade, ausência de fonte confiável ou cota excedida, os dois campos retornam `null`. Nenhum URL de logo é aceito do cliente.

### Documento do edital

`POST /api/v1/admin/exams/with-notice` requer `ADMIN` e recebe `multipart/form-data` com `document` opcional e os metadados `name`, `organizer` opcional e `year` opcional; clientes web os enviam como parâmetros da requisição para compatibilidade com o parser multipart. Em uma única transação cria o concurso e seu edital principal; quando há PDF válido, persiste o arquivo, agenda a extração e devolve `{ "exam", "syllabus", "processingJob" }`.

`POST /api/v1/admin/syllabi/{id}/document` requer `ADMIN` e recebe `multipart/form-data` com o campo `document`. O arquivo deve declarar `application/pdf` e iniciar com a assinatura `%PDF-`. O serviço calcula SHA-256, mantém o conteúdo em armazenamento local idempotente e atualiza o edital existente sem criar um novo registro.

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
| `GET /notebooks/{id}/statistics` | resumo do progresso e desempenho do caderno |
| `POST /notebooks/{id}/questions/{questionId}/attempts` | inicia tentativa |
| `POST /attempts/{id}/answers` | registra marcacao imutavel |
| `POST /attempts/{id}/complete` | conclui tentativa e aplica correcao conforme modo |
| `POST /notebooks/{id}/finish` | finaliza caderno/simulado e revela resultado quando cabivel |

`POST /notebooks/{id}/start` é idempotente enquanto o caderno está em andamento e retorna o caderno com `status`, `startedAt`, `finishedAt` e `durationSeconds`. `POST /notebooks/{id}/pause` acumula a duração já decorrida e muda o estado para `PAUSED`; `start` retoma a contagem sem perder o acumulado. `GET /notebooks/{id}/statistics` retorna `total`, `answered`, `correct`, `incorrect`, `percentage`, `averageElapsedSeconds`, `elapsedSeconds` e `answeredQuestionIds`, sempre restritos ao proprietário. `POST /notebooks/{id}/finish` encerra o caderno, calcula a duração acumulada e impede novas tentativas. Um caderno finalizado não pode ser iniciado nem finalizado novamente.

`POST /notebooks` recebe `name`, `mode` (`STUDY` ou `EXAM`), `quantity` e o objeto opcional `filters`. Para estudo direcionado, `filters` deve informar conjuntamente `exam_id`, `position_id` e uma lista não vazia `subject_ids`; o cargo precisa pertencer ao concurso e todo assunto deve estar associado ao cargo. Os filtros aceitos no MVP sao `subject_id`, `board`, `year` e `difficulty` (`EASY`, `MEDIUM` ou `HARD`). O cliente nao envia IDs de questoes: o service consulta somente questoes publicadas, persiste a lista retornada em `notebook_questions` e a composicao nunca muda. A API responde `422 VALIDATION_FAILED` quando os filtros nao encontram a quantidade solicitada, em vez de completar o caderno com questoes fora dos filtros.

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

`POST /auth/refresh` e `POST /auth/logout` usam somente o refresh token do cookie. O refresh cria uma nova sessao na mesma familia, revoga o token anterior e revoga toda a familia se um token ja rotacionado for reutilizado.

`GET /auth/me` devolve `{ "id", "email", "name", "roles" }` em `data`. Credenciais, token expirado ou revogado e usuario bloqueado retornam `401 UNAUTHENTICATED`, sem revelar qual condicao falhou.

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

`GET /api/v1/admin/question-pdf-imports/{id}` permite ao administrador que criou o job acompanhar a mesma estrutura. Estados: `PENDING`, `PROCESSING`, `COMPLETED`, `FAILED` e `CANCELLED`. O worker transmite somente páginas candidatas a questão e a lista de taxonomia ao provider de IA autorizado; o PDF original não é transmitido integralmente. Questões são criadas como `REVIEW`, sem gabarito inventado.


### Ativos visuais de questões

`GET /api/v1/question-assets/{id}` requer autenticação e entrega uma figura extraída do PDF. Figuras de questões `PUBLISHED` ficam disponíveis a qualquer usuário autenticado; ativos de itens em `REVIEW` ou `DRAFT` são restritos a ADMIN. O cliente deve buscá-los autenticadamente, sem URL pública do arquivo.

### Seleção global por edital

As questões são globais e classificadas por taxonomia canônica; não pertencem a concurso ou edital. Ao criar um caderno, o filtro opcional `syllabus_id` restringe a seleção a questões cujos assuntos canônicos constem no edital informado. Banca, concurso de origem e ano são apenas metadados de cada questão.

### Histórico de importações de PDFs

`GET /api/v1/admin/question-pdf-imports?page=1&per_page=25` requer `ADMIN` e retorna somente os jobs criados pelo administrador autenticado, em ordem decrescente de criação. A resposta é paginada e cada item possui o mesmo resumo do job individual. Isso permite retomar o acompanhamento após sair da tela, sem expor documentos de outro administrador.

### Proveniência de questões importadas

Questões criadas pela importação de PDF preservam internamente o `source_pdf_job_id` e as páginas (`source_pdf_pages`) de onde foram extraídas. Essa proveniência é imutável e será usada pelo assistente para limitar evidências à fonte original da questão.

| Dúvida com fonte da questão | `POST /questions/{id}/pdf-assistance` recebe `{ "question": "..." }`, exige autenticação e responde com conteúdo da IA, provider/modelo e páginas de evidência; usa exclusivamente o PDF e as páginas associados à questão importada. |
