# Modelo de Dados

## Convencoes

MySQL 8, `utf8mb4`, chaves primarias UUID (armazenadas como `BINARY(16)` via tipo Doctrine) e timestamps UTC. Todas as tabelas possuem `created_at`; entidades mutaveis tambem possuem `updated_at`. Exclusao editorial usa `archived_at` quando necessaria; tentativas e respostas nao sofrem exclusao fisica.

## Identidade e catalogo

| Entidade | Campos principais | Relacionamentos |
| --- | --- | --- |
| `users` | email unico, name, password_hash, status | N:N com `roles`; possui sessoes, cadernos e dados pessoais |
| `roles` | code unico (`ADMIN`, `USER`) | N:N com usuarios |
| `user_roles` | user_id, role_id | tabela de associacao |
| `auth_sessions` | user_id, family_id, device_name opcional, refresh_token_hash, expires_at, revoked_at | muitas sessoes por usuario |
| `auth_events` | user_id opcional, event, ip_hash, occurred_at | trilha de login/falha/bloqueio |
| `exams` | name, organizer, year | possui cargos |
| `exam_positions` | exam_id, name, emphasis | possui editais |
| `syllabi` | exam_position_id, name, published_at, source_url | possui disciplinas e vincula questoes |
| `subjects` | syllabus_id, parent_id opcional, name, sort_order | arvore por auto-relacao; `parent_id` nulo para raiz |
| `tags` | name unico normalizado | N:N com questoes |

Um assunto pertence a um edital. Isso impede que uma mesma sigla com significado diferente seja compartilhada acidentalmente. Reuso futuro deve ocorrer por clonagem controlada ou por um catalogo global explicitamente versionado, nao por acoplamento oculto.

## Banco de questoes

| Entidade | Campos principais | Relacionamentos |
| --- | --- | --- |
| `questions` | statement, correct_option_id, difficulty, board, year, source, reference_url, origin, status, syllabus_id | possui alternativas; N:N com assuntos e tags |
| `question_options` | question_id, label (`A`-`E`), content, image_path, sort_order | cinco por questao na regra inicial |
| `question_subjects` | question_id, subject_id | associacao N:N |
| `question_tags` | question_id, tag_id | associacao N:N |
| `question_imports` | created_by, format, filename, status, totals, report_json | lote administrativo |
| `question_import_rows` | import_id, line_number, payload_json, status, errors_json, question_id opcional | auditoria de cada linha |
| `ai_question_generations` | requested_by, provider, model, prompt, subject_id, generated_at | audita questoes propostas |

`correct_option_id` deve pertencer a alternativa da propria questao, validacao aplicada pelo caso de uso. `status` admite `DRAFT`, `REVIEW`, `PUBLISHED`, `VOID`. Apenas `PUBLISHED` aparece em selecoes comuns; `VOID` preserva rastreabilidade e nao gera correcao positiva/negativa.

Duplicidade de importacao nao depende de hash como decisao final: um hash normalizado de enunciado e alternativas localiza candidatos, e o administrador confirma os casos ambiguos. Linhas invalidas sao mantidas no relatorio e jamais inseridas parcialmente.

## Estudo e desempenho

| Entidade | Campos principais | Relacionamentos |
| --- | --- | --- |
| `notebooks` | user_id, name, type, mode, status, filters_json, duration_seconds | possui itens e tentativas |
| `notebook_questions` | notebook_id, question_id, position, discipline_snapshot | lista congelada e ordenada |
| `attempts` | user_id, notebook_id, question_id, number, started_at, completed_at, context, final_answer_id | uma tentativa por execucao de item |
| `answers` | attempt_id, option_id opcional, sequence, submitted_at, elapsed_seconds, changed_from_answer_id opcional | historico imutavel de marcacoes |
| `reviews` | user_id, question_id, status, next_review_at, review_count, last_review_at, manual_marked | fila individual |
| `review_events` | review_id, attempt_id opcional, event, occurred_at, payload_json | historico de agendamento e desempenho |
| `recommendation_snapshots` | user_id, subject_id, reason, score, confidence, metrics_json, generated_at, dismissed_at | explicabilidade e historico de recomendacao |

`notebooks.type` e `PRACTICE` ou `SIMULATION`; `mode` e `STUDY` ou `EXAM`; `status` pode ser `DRAFT`, `IN_PROGRESS`, `PAUSED` ou `FINISHED`. A pausa acumula `duration_seconds` e a retomada reinicia apenas o intervalo ativo. Simulados devem usar `EXAM`. A distribuicao planejada do simulado e armazenada em `filters_json`/configuracao estruturada; os itens efetivos ficam apenas em `notebook_questions`.

Uma `attempt` representa a interacao do usuario com uma questao em uma execucao. `answers` mantem cada alteracao, inclusive pulo com `option_id` nulo. O resultado final e derivado da ultima resposta submetida antes da finalizacao, comparada ao gabarito valido naquele momento. `final_answer_id` e uma referencia de conveniencia definida na conclusao; o historico continua em `answers`.

Indices essenciais: `answers(attempt_id, sequence)`, `attempts(user_id, completed_at)`, `attempts(question_id)`, `reviews(user_id, next_review_at)`, `question_subjects(subject_id, question_id)` e `notebook_questions(notebook_id, position)`.

## Assistente

| Entidade | Campos principais | Relacionamentos |
| --- | --- | --- |
| `ai_conversations` | user_id, subject_id opcional, notebook_id opcional, question_id opcional, mode | possui mensagens privadas |
| `ai_messages` | conversation_id, role, content, context_summary_json, provider, model, latency_ms | auditoria de interacao |

O contexto enviado ao provedor nao e salvo integralmente por padrao; registra-se apenas resumo minimizado e metadados operacionais. Retencao de mensagens tera configuracao administrativa futura.

## Cardinalidades resumidas

```text
Exam 1--N ExamPosition 1--N Syllabus 1--N Subject
Question N--N Subject; Question N--N Tag; Question 1--N QuestionOption
User 1--N Notebook 1--N NotebookQuestion N--1 Question
User 1--N Attempt 1--N Answer; Attempt N--1 Question
User 1--N Review N--1 Question
User 1--N AIConversation 1--N AIMessage
```

## Taxonomia canônica de assuntos

A migration `005_taxonomy_foundation.sql` introduz uma taxonomia global sem alterar ou remover a árvore legada em `subjects`. A coexistência é deliberada: os vínculos atuais continuam operacionais até que o marco M0 conclua a migração explícita de conteúdo programático e questões.

| Entidade | Finalidade |
| --- | --- |
| `taxonomy_subjects` | assunto canônico global, com `parent_id` recursivo, `slug`, descrição, nível e estado ativo |
| `taxonomy_subject_aliases` | sinônimos normalizados que resolvem para o assunto canônico |
| `taxonomy_subject_merges` | auditoria de fusões administrativas, com origem, destino, autor e motivo |
| `question_taxonomy_subjects` | ligação N:N entre questões e assuntos canônicos; coexistirá com `question_subjects` durante a migração explícita |

A migration `006_question_taxonomy_subjects.sql` cria a associação paralela entre questões e taxonomia canônica. Ela não remove nem preenche automaticamente `question_subjects`; a migração de vínculos deve ser revisável e idempotente.

A profundidade de `taxonomy_subjects` é ilimitada. O serviço de domínio do marco M0.2 impedirá ciclos, normalizará nomes/aliases e fará a reatribuição transacional de referências quando houver fusão. A IA poderá apenas propor reconciliações; decisões de baixa confiança exigem revisão humana.
