# Relatório técnico — question-learning-interactions

Data: 2026-09-29

## Entrega

O contexto `QuestionLearning` foi entregue com estado pessoal por questão, anotações privadas, comentários públicos, relatos auditáveis, sinais adaptativos e explicações por IA persistidas. Os fluxos HTTP usam DTOs, serviços, interfaces de repositório, Doctrine e envelopes padronizados; a SPA consome os contratos pelo módulo DDD correspondente.

## Persistência e isolamento

A migration `038_question_learning_interactions.sql` cria tabelas para interações, anotações, comentários, relatos e seus eventos, sinais de aprendizagem e execuções de explicação. Leituras e alterações pessoais são sempre condicionadas ao usuário autenticado. Teste transacional Doctrine confirma que notas e explicações de proprietários distintos não se misturam.

## Sinais e explicações

Tentativas concluídas e alterações de não domínio geram eventos imutáveis por assunto canônico. A explicação possui execução persistida, modo `CONCEPTUAL_ONLY` antes da conclusão e `POST_ANSWER` após tentativa finalizada. O validador rejeita revelação de gabarito no modo conceitual; respostas inválidas, erros do provider e detalhes internos não são enviados ao cliente.

## Operação do worker

`question-explanation-worker` executa `bin/process-question-explanations.php` em ciclo de cinco segundos. Ele reutiliza a imagem `concursos-codex-runner`, o volume de workspaces e a autenticação Codex, com timeout por `EXPLANATION_CODEX_TIMEOUT` e socket Docker autorizado. O provider drena stdout e stderr sem registrar prompt, contexto ou conteúdo de execução nos logs.

Para operar o runner, mantenha o volume `codex_auth` autenticado e a rede definida por `EXPLANATION_CODEX_NETWORK` acessível ao container de runner. Sem uma execução pendente, o worker registra somente `processed:false`.

## Frontend

O módulo `QuestionLearning` contém contratos Domain, casos de uso Application, repositório Axios e composição no container. A barra reutilizável de marcações é usada no banco de questões e na execução de cadernos, com atualização otimista e rollback. O painel lateral oferece anotação, comentários, relato e explicação com estados de carregamento, erro, vazio e layout responsivo. A lista pública tem filtros de favoritas, revisar depois e não dominei, preservando paginação real.

## Validações

- Migration aplicada e reexecutada sem pendências.
- `docker compose config --quiet` aprovado.
- Worker `question-explanation-worker` ativo e ocioso confirmado por log sanitizado.
- Suíte focal: 16 testes e 48 asserções aprovadas.
- `docker compose exec -T frontend npm run build` aprovado; permanece o aviso não bloqueante de bundle acima de 500 kB.
- `git diff --check` aprovado.

## Limitações conhecidas

A explicação depende de sessão válida do Codex no volume `codex_auth` e de conectividade do runner. Erros de provider mantêm a execução em estado de falha retentável, sem expor detalhes internos.
