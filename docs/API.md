# API REST

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
| Concursos | `GET, POST /exams`; `GET, PUT, DELETE /exams/{id}` |
| Cargos | `GET, POST /exams/{examId}/positions`; `PATCH, DELETE /positions/{id}` |
| Editais | `GET, POST /positions/{positionId}/syllabi`; `PATCH, DELETE /syllabi/{id}` |
| Assuntos | `GET /syllabi/{id}/subjects`; `POST /subjects`; `GET, PATCH, DELETE /subjects/{id}` |
| Tags | `GET, POST /tags` |
| Questoes | `GET /questions`; `POST /questions`; `GET, PATCH /questions/{id}`; `POST /questions/{id}/publish` |
| Importacao | `POST /question-imports` (arquivo JSON/CSV); `GET /question-imports/{id}`; `POST /question-imports/{id}/commit` |
| Revisão editorial | `GET /admin/questions/drafts`; `POST /admin/questions/{id}/publish` |
| Taxonomia | `GET, POST, PATCH /admin/taxonomy/subjects` (lista paginada e cria assunto canônico); `POST /admin/taxonomy/aliases` (cria alias canônico) |
| Taxonomia editorial | `PUT /admin/questions/{id}/taxonomy-subjects` recebe `{ "taxonomy_subject_ids": ["uuid"] }` e substitui as associações canônicas da questão para usuários ADMIN |
| Usuarios | `GET /users`; `PATCH /users/{id}/roles`; `PATCH /users/{id}/status` |

`GET /questions` aceita filtros `syllabus_id`, `subject_id`, `tag`, `board`, `year`, `difficulty`, `status` (admin) e `origin`. A importacao primeiro valida e cria relatorio; `commit` insere apenas linhas validas explicitamente aprovadas. Assim nao ha insercao silenciosa.



### Preview de importacao

`POST /api/v1/question-imports` requer `ADMIN` e recebe:

```json
{ "format": "JSON", "content": "[...]" }
```

ou `{ "format": "CSV", "content": "statement,options,correct_option\\n..." }`.
A resposta contem `validRows`, `invalidRows` e `rows` com o numero da linha, a situacao e os erros. Uma linha repetida recebe `DUPLICATE_CANDIDATE`. O preview persiste o relatorio e cada linha, associado ao administrador que o enviou, mas nao cria questoes. A confirmacao posterior podera gravar somente linhas validas explicitamente aprovadas.
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

`POST /notebooks` recebe `name`, `mode` (`STUDY` ou `EXAM`), `quantity` e o objeto opcional `filters`. Os filtros aceitos no MVP sao `subject_id`, `board`, `year` e `difficulty` (`EASY`, `MEDIUM` ou `HARD`). O cliente nao envia IDs de questoes: o service consulta somente questoes publicadas, persiste a lista retornada em `notebook_questions` e a composicao nunca muda. A API responde `422 VALIDATION_FAILED` quando os filtros nao encontram a quantidade solicitada, em vez de completar o caderno com questoes fora dos filtros.

## Desempenho e revisoes

| Metodo e rota | Regra |
| --- | --- |
| `GET /dashboard/me` | resumo semanal, fortes, prioridades e revisoes |
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
| `POST /assistant/conversations` | inicia conversa com escopo permitido |
| `POST /assistant/conversations/{id}/messages` | envia pergunta e gera resposta contextualizada |
| `GET /assistant/conversations` | lista apenas conversas do usuario |
| `POST /assistant/questions/generate` | gera rascunhos editoriais, somente `ADMIN` |

O endpoint contextual de questao recebe `question_id` e `attempt_id`. Para simulado aberto em modo prova, a API aplica politica de contexto restrito antes de chamar o provider.

## Codigos importantes

`400` entrada invalida, `401` autenticacao ausente/expirada, `403` papel ou propriedade insuficiente, `404` recurso inexistente ou invisivel, `409` estado incompatível (por exemplo, concluir duas vezes), `422` validacao de dominio, `429` rate limit e `500` erro inesperado com request id.

## Contratos de autenticacao

`POST /auth/login` recebe `{ "email": "user@example.com", "password": "...", "device_name": "opcional" }`. Em sucesso, devolve `data` com `access_token` e `user`; o refresh token opaco e entregue exclusivamente em cookie `HttpOnly`, `Secure` e `SameSite=Lax`.

`POST /auth/refresh` e `POST /auth/logout` usam somente o refresh token do cookie. O refresh cria uma nova sessao na mesma familia, revoga o token anterior e revoga toda a familia se um token ja rotacionado for reutilizado.

`GET /auth/me` devolve `{ "id", "email", "name", "roles" }` em `data`. Credenciais, token expirado ou revogado e usuario bloqueado retornam `401 UNAUTHENTICATED`, sem revelar qual condicao falhou.

Os services recebem Request DTOs e retornam Response DTOs; JWT, cookies, Argon2id e Doctrine pertencem aos adaptadores de infraestrutura.

### Navegação de questões congeladas

GET /api/v1/notebooks/{id}/questions?page=1&per_page=25 requer autenticação e devolve uma lista paginada de questões na ordem gravada em notebook_questions. A rota filtra o caderno pelo proprietário, preserva a seleção congelada e não repete os filtros usados na criação. Um caderno inexistente ou de outro usuário retorna 404 RESOURCE_NOT_FOUND.
