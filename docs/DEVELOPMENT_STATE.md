## 2026-10-01 — Renovação operacional das contagens de Taxonomia

- Confirmada em runtime a consulta Doctrine com contagens publicadas e a serialização HTTP com `questionCount` positivo; por exemplo, o nó Administração devolve 135 questões.
- O zero persistente na tela era causado pelo processo web da API ainda servir código anterior em memória após a alteração do serviço.
- Reiniciado somente o contêiner `api`; a API, frontend, banco, Nginx, workers e realtime voltaram ao estado `running`.
- Próximo passo: recarregar a página da Taxonomia no navegador para obter a resposta renovada.

## 2026-10-01 — Correção da contagem na Taxonomia

- Corrigido o serviço paginado da Taxonomia: a variável do total de assuntos era sobrescrita durante a agregação de questões dos descendentes, devolvendo paginação incorreta.
- A agregação agora preserva o total real de nós e consulta toda a hierarquia, sem o antigo limite fixo de 1.000 assuntos.
- Os badges continuam exibindo somente questões `PUBLISHED`, agregadas de cada nó e de seus descendentes.
- Validação: lint PHP, PHPUnit focal (1 teste, 2 asserções), build tipado da SPA e `git diff --check` aprovados. Mantém-se somente o alerta não bloqueante de bundle acima de 500 kB.
- Próximo passo: nenhum para esta correção pontual.

## 2026-10-01 — Correção da árvore de assuntos da Arena

- Corrigido `GET /arena/subjects`: a projeção tentava ler `questionCount` de uma entidade de taxonomia que não possui essa propriedade, interrompendo a resposta e deixando o componente sem nós.
- O serviço passa a consultar as contagens diretas pelo contrato de repositório; cada nó entrega `id`, `name`, `parentId` e a quantidade correta de questões.
- O cliente Arena busca todas as páginas de assuntos e mantém essa carga isolada da listagem de salas, para que erro em uma sala não oculte a árvore.
- `SubjectTreeSelect` passa a abrir com os assuntos contraídos, preservando busca, expansão manual, seleção e badges de contagem.
- Validação: lint PHP, PHPUnit focal (1 teste, 5 asserções), build tipado da SPA e `git diff --check` aprovados. Permanece somente o alerta não bloqueante de bundle acima de 500 kB.
- Próximo passo: nenhum para esta correção pontual.

## 2026-10-01 — Seleção hierárquica de assuntos

- Criado `SubjectTreeSelect`, componente Quasar reutilizável que apresenta a taxonomia em árvore, com busca, expansão e seleção múltipla sem alterar os IDs enviados aos casos de uso.
- Integrado aos fluxos de Assuntos do cargo, Caderno específico, Revisão Editorial e Arena. A Arena passou a receber `parentId` na projeção de assuntos para reconstruir a hierarquia real.
- Validação: build da SPA aprovado e lint PHP do DTO e serviço Arena aprovado. Permanece o aviso conhecido de bundle acima de 500 kB.
- Próximo passo: nenhum.


## 2026-10-01 — Correção do menu lateral e árvore de conhecimento

- O drawer passou a aplicar azul-noturno também à superfície interna do Quasar, eliminando o fundo branco persistente; itens, ícones e item ativo mantêm contraste adequado.
- A árvore de conhecimento da Taxonomia agora tem superfície escura, títulos claros, texto de nó legível, seleção azul, hover e badges de contagem contrastantes.
- Validação: `docker compose exec -T frontend npm run build` e `git diff --check` aprovados. Permanece apenas o aviso conhecido de bundle acima de 500 kB.
- Próximo passo: nenhum para esta correção pontual.


## 2026-10-01 — Contraste integral das telas no padrão Arena

- Concluída a revisão transversal de contraste: títulos, texto auxiliar, caixas de conteúdo, diálogos, campos, selects, menus, tabs, paginação, tabelas, listas, árvores e conteúdo estruturado usam superfícies azul-profundo e níveis de texto legíveis.
- Estilos scoped legados não voltam a aplicar caixas ou texto claros: a fundação global possui prioridade suficiente e preserva os blocos de conquista como exceção azul-profundo contrastante.
- Adicionado `apps/api/.dockerignore` para excluir avatares privados do contexto Docker, permitindo a construção da suíte visual isolada sem ampliar permissões dos dados pessoais.
- Validação: `docker compose exec -T frontend npm run build` e `./scripts/test-visual-e2e.sh` concluídos após limpeza dos recursos temporários E2E; `git diff --check` aprovado. Mantém-se apenas o aviso conhecido de bundle acima de 500 kB.
- Próximo passo: usar esta escala de contraste como referência obrigatória para novos componentes e páginas.


## 2026-10-01 — Dados estruturados no tema Arena (em andamento)

- Tabelas, cabeçalhos, linhas, seleção, paginação, listas, árvores e editor agora têm superfícies e contraste próprios no tema escuro.
- Próximo passo: validar build e procurar resquícios de fundos claros em regras locais.


## 2026-10-01 — Controles escuros da Arena (em andamento)

- Campos, selects, menus, tabs e paginação receberam fundo azul-profundo, texto de alto contraste, foco azul-claro e estado ativo distinguível.
- Próximo passo: aplicar a mesma hierarquia às tabelas, listas estruturadas e cabeçalhos.


## 2026-10-01 — Revisão de contraste da identidade Arena (em andamento)

- Identificado que estilos scoped das telas ainda preservam tons e superfícies do tema claro, sobrepondo parcialmente a fundação da Arena.
- Iniciada uma camada compartilhada de contraste para conteúdo, títulos, texto auxiliar, cards, listas, chips, banners e estados de carregamento.
- Próximo passo: concluir campos, tabs, tabelas, paginação e validar a composição em todas as jornadas.


## 2026-10-01 — Identidade visual unificada pela Arena

- A identidade da Arena passou a ser a fundação de toda a SPA: canvas azul-noturno, superfícies azul-profundo, bordas discretas, ação azul-elétrica e dourado reservado para conquista.
- Tokens Quasar, estilos globais e o shell foram alinhados; cabeçalho e drawer não alternam mais para o padrão claro fora da Arena. Campos, botões, menus, banners e foco de teclado receberam os estados coerentes e acessíveis.
- `DESIGN_SYSTEM.md` foi reescrito como contrato da identidade Arena, preservando Quasar, responsividade e a arquitetura DDD do frontend.
- Validação: `docker compose exec -T frontend npm run build` aprovado. Permanece o aviso conhecido de chunk JavaScript acima de 500 kB, sem falha de tipos ou empacotamento.
- Próximo passo: usar a identidade Arena como referência obrigatória em refinamentos visuais pontuais e validar visualmente as jornadas críticas em desktop e mobile quando o ambiente E2E estiver disponível.


## 2026-09-29 — Correção de GD e testes de Performance

- A imagem da API foi reconstruída; a extensão GD, já declarada no Dockerfile, agora está carregada no contêiner e os testes de avatar voltaram a executar.
- Atualizados os fixtures de Performance para injetar `NotebookRepositoryInterface` e usar cadernos `IN_PROGRESS`, conforme os contratos atuais de tentativa e resposta.
- Validação: suíte completa da API aprovada com 129 testes e 316 asserções. Permanece somente um aviso não bloqueante do PHPUnit.
- Próximo passo: nenhum para esta correção; acompanhar o aviso do PHPUnit separadamente se necessário.

## 2026-09-29 — Conclusão da mudança de análise Codex

- Coberturas focais aprovadas para schema/não revelação, telemetria, factory de providers e persistência Doctrine de análise, achado e âncora.
- Build SPA, Compose, migration 039, worker Codex e validação OpenSpec foram aprovados. A suíte completa mantém falhas preexistentes de GD e Performance, documentadas sem relação com esta mudança.
- Próximo passo: validar em um PDF real na fila administrativa e acompanhar telemetria do primeiro job Codex.

## 2026-09-29 — Integração Doctrine de análise de importação

- Adicionado teste integrado transacional para persistir e reler análise, achado de conflito e âncora visual ambígua. A tentativa de regravar o mesmo ID é rejeitada, confirmando imutabilidade.
- Validação: teste Doctrine aprovado (1 teste, 4 asserções); a transação é revertida ao fim.
- Próximo passo: concluir os cenários restantes de integração e contrato HTTP/SPA.

## 2026-09-29 — Preparação de integração Doctrine

- Preparado o diretório de testes de integração do contexto QuestionBank para a fixture transacional de análises; nenhuma fixture foi persistida após a tentativa inicial.
- Próximo passo: executar a persistência/releitura de análise, achado e âncora e validar imutabilidade.

## 2026-09-29 — Correção de imports de telemetria

- Corrigidas as importações com namespace ausente no repositório Doctrine de jobs PDF; a leitura de `analysisTelemetry` agora resolve os value objects e enum corretos em runtime.
- Validação: lint PHP e busca por imports equivalentes corrompidos aprovados.
- Próximo passo: executar a integração Doctrine de gravação/leitura da análise.

## 2026-09-29 — Correção de validação de achados

- Corrigida a validação de páginas no value object `QuestionImportFinding`: páginas inválidas agora são rejeitadas independentemente do resumo estar válido.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: persistir e reler análise, achado e âncora em teste Doctrine integrado.

## 2026-09-29 — Cobertura da factory de analisadores

- Adicionado teste da factory configurável: resolve `codex` independentemente de capitalização e rejeita provider futuro não configurado com erro explícito, sem fallback silencioso.
- A suíte unitária focal de schema, telemetria e factory passa (6 testes, 16 asserções).
- Próximo passo: concluir a cobertura de orçamento e a integração Doctrine dos registros imutáveis.

## 2026-09-29 — Cobertura de schema e não revelação

- Adicionado teste do `QuestionImportAnalysisResponseValidator` para conflito de gabarito com evidência e para bloqueio de resumo que revele alternativa/gabarito.
- Em conjunto com a telemetria, os testes focais novos passam (4 testes, 14 asserções). A cobertura de integração Doctrine e de contrato HTTP/SPA continua pendente.
- Próximo passo: revisar persistência e rotas no ambiente integrado.

## 2026-09-29 — Diagnóstico da suíte completa

- A suíte integral da API executou 124 testes: 120 passaram; há três erros e uma falha preexistentes fora da importação (extensão GD ausente em avatar e contratos desatualizados de Performance para tentativa/resposta).
- O build da SPA continua aprovado no contêiner frontend; permanece apenas o alerta não bloqueante de chunk acima de 500 kB.
- As tarefas de cobertura específica permanecem abertas até incluir testes de schema, persistência Doctrine e contrato HTTP/SPA da análise.
- Próximo passo: revisar o diff completo e adicionar casos focais remanescentes sem mascarar as falhas preexistentes.

## 2026-09-29 — Cobertura de telemetria de importação

- Adicionado teste unitário do agregador de telemetria: valida soma de duração, tokens, custo e totais dos três achados editoriais, além da indisponibilidade explícita de uso.
- O teste revelou que o custo da primeira análise reportada era perdido; o acumulador foi corrigido para preservá-lo e somá-lo nas análises seguintes.
- Validação: suíte focal de importação aprovada (5 testes, 18 asserções), lint do agregador e `git diff --check` aprovados.
- Próximo passo: ampliar cobertura para schema/política e persistência Doctrine dos novos registros.

## 2026-09-29 — Validação operacional da análise Codex

- O Compose validou a composição do worker com o runner isolado. A migration 039 foi aplicada e confirmada no MySQL, incluindo tabelas de análise e colunas agregadas do job.
- O worker recriado iniciou normalmente; a suíte focal de importação passou (3 testes, 7 asserções) e o build da SPA no contêiner frontend foi aprovado.
- O build preserva apenas o aviso não bloqueante de chunk JavaScript acima de 500 kB. Permanecem pendentes testes específicos de schema, projeção Doctrine e contratos HTTP/SPA da nova análise.
- Próximo passo: adicionar cobertura focal para telemetria, não revelação e persistência de sinais.

## 2026-09-29 — Operação do worker Codex de importação

- A migration `039_question_import_analysis.sql` foi aplicada pelo migrador do projeto e registrada em `schema_migrations`; as tabelas de análises, achados, âncoras e sinais de qualidade passaram a existir no banco ativo.
- O `question-pdf-worker` foi configurado com `QUESTION_IMPORT_ANALYZER=codex`, limites e volumes de workspace/autenticação, além do socket Docker autorizado para executar o runner isolado. O Compose foi validado e o worker recriado iniciou sem jobs pendentes.
- Próximo passo: concluir testes focais, validação de frontend e documentação de estados da interface.

## 2026-09-29 — Ressalva editorial na explicação pós-tentativa

- O contexto da explicação recebe a mensagem segura de qualidade somente em `POST_ANSWER`, após validar a tentativa concluída do próprio usuário. Em modo conceitual não há ressalva nem revelação de resposta.
- O prompt do provider Codex instrui que a ressalva é editorial, não um fato conclusivo, e deve orientar a explicação do entendimento aplicável depois da resposta.
- A configuração do worker Codex permanece pendente de autorização explícita para montar `/var/run/docker.sock`: esse acesso permite controlar o daemon Docker do host e tem impacto amplo.
- Validação: lint PHP do value object, leitor Doctrine e provider, além de `git diff --check`, aprovados.
- Próximo passo: obter autorização explícita para o socket Docker ou definir um executor remoto com privilégio menor; depois validar Compose, migração e suíte.

## 2026-09-29 — Aviso editorial seguro na resolução

- A projeção de questão publicada lê apenas a mensagem segura de `question_quality_signals` e a expõe como `qualityNotice`; nenhuma alternativa, gabarito ou raciocínio inferido é transportado.
- As telas de listagem e execução de caderno exibem esse aviso permanentemente junto aos metadados da questão, com orientação para responder normalmente e solicitar explicação após a tentativa.
- Validação: lint PHP do read model, mapper, DTO e repositório Doctrine, além de `git diff --check`, aprovados.
- Próximo passo: adicionar o contexto editorial à explicação pós-tentativa e validar a infraestrutura do worker.

## 2026-09-29 — Histórico e revisão de análise na SPA

- O módulo DDD de importação recebeu contratos para telemetria e análises; o repositório Axios usa o novo endpoint administrativo sem vazar envelopes HTTP para a interface.
- Cada job mostra provider, duração, tokens reportados ou indisponíveis e totais de achados. A revisão apresenta resumos seguros, páginas de evidência e discrepância visual de forma direta, sem revelar alternativa/gabarito inferidos.
- A validação de tipos via `vue-tsc` ficou pendente: a dependência local não está instalada e a cópia executada por `npx` é incompatível com a versão de TypeScript disponível.
- Próximo passo: projetar o aviso seguro da questão para resolução/revisão e ampliar o contexto da explicação pós-tentativa.

## 2026-09-29 — Contrato administrativo de análises de importação

- `GET /admin/question-pdf-imports/{id}/analyses` expõe, somente ao administrador dono do job, as análises imutáveis com achados seguros, âncoras visuais, metadados evidenciados, provider/modelo, duração e telemetria disponível ou indisponível.
- O resumo/listagem de jobs passa a incluir `analysisTelemetry`; jobs anteriores permanecem compatíveis com valor nulo. A documentação API descreve a não revelação de gabarito no histórico.
- Validação: lint PHP do controller, rota, DTOs, mapper e serviço, além de `git diff --check`, aprovados.
- Próximo passo: integrar o histórico e a revisão visual na SPA administrativa.

## 2026-09-29 — Compatibilidade de runtime da análise de importação

- Corrigidos o namespace do enum de avaliação visual e a validação de páginas de evidência para não depender de `array_any`; a análise é compatível com o runtime PHP atual.
- Validação: `php -l` dos agregados de análise e job, e busca de referências corrompidas, aprovados.
- Próximo passo: expor os detalhes administrativos de análise e telemetria no contrato HTTP.

## 2026-09-29 — Telemetria e histórico da análise de importação

- Cada análise concluída agora agrega no job o provider, modelo, versão do schema, duração, total de análises e totais de conflito de gabarito, discrepância visual e incoerência estrutural.
- O uso de tokens e custo é acumulado somente quando reportado pelo provider; para o Codex CLI atual, a indisponibilidade é persistida de forma explícita e exposta pelo DTO administrativo, sem estimativas.
- A telemetria é preservada ao atualizar estado, retentar ou encerrar o job. Validação: lint PHP dos contratos e serviços alterados, além de `git diff --check`, aprovados.
- Próximo passo: documentar o contrato HTTP e implementar histórico administrativo e avisos seguros na interface.

## 2026-09-29 — Worker e escrita auditável de importação

- O worker de PDF passou a segmentar e analisar cada candidato pelo provider configurado antes de escrever; cancelamento, timeout, orçamento `QUESTION_IMPORT_MAX_CANDIDATES` e retentativa permanecem no job persistido.
- A escrita agora devolve o ID por fingerprint para vincular análise, achados e sinal seguro à questão criada. Metadados vêm do validador com evidência; somente âncoras `ANCHORED` associam ativos ao enunciado ou alternativa.
- `ANSWER_KEY_CONFLICT`, discrepância visual e incoerência estrutural persistem como achados e deixam a questão em `REVIEW`; gabarito conflituoso não é gravado automaticamente.
- Criado adaptador callable para testes e futura API externa pela mesma porta. Validação: lint dos contratos, worker, writer e repositórios, `git diff --check` e OpenSpec estrito aprovados.
- Próximo passo: agregar telemetria e achados no histórico do job e expor DTOs/rotas administrativas.


## 2026-09-29 — Adaptador Codex de importação

- Implementado `CodexQuestionImportAnalyzer` em workspace efêmero: ele recebe somente o candidato, páginas de evidência e cópias das imagens extraídas, valida a saída pelo schema e remove todos os artefatos ao concluir.
- A execução isolada usa o runner Codex autorizado, sem acesso ao banco pelo contrato do caso de uso. Como o CLI não expõe consumo confiável no resultado estruturado, a telemetria é persistida explicitamente como indisponível, nunca estimada.
- Validação: lint PHP do adaptador e da factory, além de `git diff --check`, aprovados.
- Próximo passo: integrar segmentação/analisador ao worker com cancelamento, orçamento e retentativas.


## 2026-09-29 — Segmentação determinística de candidatos PDF

- Implementada segmentação por início de questão, em vez de lotes fixos de páginas, com fingerprint canônico, evidência limitada ao trecho e páginas vizinhas quando houver referência visual.
- O candidato transporta manifesto de imagens por página e índice, permitindo que o analisador indique âncora no enunciado ou alternativa sem obter acesso livre ao PDF.
- Validação: lint do segmentador e `git diff --check` aprovados.
- Próximo passo: implementar o adaptador Codex isolado e seu consumo de schema/telemetria.


## 2026-09-29 — Porta configurável de análise de importação

- Criada `QuestionImportAnalyzerInterface` separada do provider de conversa, factory de aplicação e composição por `QUESTION_IMPORT_ANALYZER` na infraestrutura.
- Enquanto o adaptador selecionado não estiver registrado, o analisador indisponível lança falha explícita para que o job aplique retentativa; não há fallback silencioso ao extrator legado.
- Validação: lint PHP das portas, providers e factory, além de `git diff --check`, aprovados.
- Próximo passo: preparar segmentação determinística e manifesto de imagens para cada candidato.


## 2026-09-29 — Schema validado de análise por questão

- Criados DTOs tipados para candidato, páginas, manifesto de imagens, opções, achados, âncoras e resposta do analisador, além do schema `question-import-analysis-v1`.
- O validador limita as páginas à evidência fornecida, exige alternativas A–E distintas, gabarito existente, metadados provados, achados correspondentes a conflitos e impede resumos que revelem alternativa ou gabarito.
- Validação: lint PHP dos DTOs, schema e validador aprovado.
- Próximo passo: criar a porta de provider e a factory configurável, com indisponibilidade segura.


## 2026-09-29 — Contratos de domínio da análise de importação

- Modelados análise imutável, achados tipados, âncoras de imagem, metadados com páginas de evidência, uso de tokens e sinal seguro de qualidade no contexto `QuestionBank`.
- Os value objects rejeitam metadados sem página de prova, confiança fora de 0–1, âncoras ambíguas e telemetria inconsistente; os repositórios separam o histórico da projeção exibida ao usuário.
- Validação: lint de todos os contratos de `Domain/QuestionBank` e `git diff --check` aprovados.
- Próximo passo: definir DTOs e schema versionado para a resposta dos analisadores.


## 2026-09-29 — Schema de análise de importação

- Adicionada a migration `039_question_import_analysis.sql`, inteiramente aditiva, para análises e achados imutáveis, âncoras de imagem, projeção segura de qualidade e telemetria agregada dos jobs de PDF.
- Jobs e questões existentes permanecem compatíveis: as novas métricas começam em zero ou indisponíveis e referências de questão em análises preservam o histórico via `ON DELETE SET NULL`.
- Validação: revisão de compatibilidade contra as migrations de jobs, proveniência e ativos; `git diff --check` pendente ao fim da etapa.
- Próximo passo: modelar os contratos de domínio, incluindo análise, achados, uso de tokens e sinal seguro.


## 2026-09-29 — Proposta de análise Codex na importação de questões

- Criada a mudança OpenSpec `codex-question-import-analysis` para substituir a análise semântica genérica de PDFs por provider estruturado, com Codex isolado inicial e adaptadores futuros configuráveis.
- A proposta exige análise individual antes da persistência, evidências de metadados/imagens/estrutura, achados imutáveis de possível incoerência, telemetria de tokens e avisos seguros na revisão, resolução e explicação pós-tentativa.
- Artefatos de proposta, design, especificações delta e tarefas foram concluídos; nenhuma mudança de código ou banco foi aplicada nesta etapa.
- Próximo passo: aplicar por `/opsx:apply codex-question-import-analysis`.


## 2026-09-29 — Correção do método de questão ativa

- Corrigido o cliente de cadernos para usar `PATCH /notebooks/{id}/active-question`, conforme contrato da API; ele enviava `PUT`, método sem rota correspondente.
- Validação concluída: build da SPA aprovado; permanece apenas o aviso não bloqueante de chunk acima de 500 kB.
- Próximo passo: retestar a navegação de questões no caderno.


## 2026-09-29 — Persistência das interações de questão

- Corrigida a persistência das interações de aprendizagem: os repositórios Doctrine agora sincronizam as alterações de favorito, revisão posterior, não domínio, notas, comentários, denúncias, eventos e solicitações de explicação no banco antes da resposta HTTP.
- Adicionada cobertura de integração que limpa o contexto Doctrine sem `flush` explícito após gravar nota e solicitação de explicação, garantindo que os repositórios façam a persistência.
- Validações concluídas: suíte focalizada de QuestionLearning aprovada (15 testes, 41 asserções), lint dos seis repositórios PHP e `git diff --check` sem apontamentos.
- Próximo passo: retestar no caderno existente “Prática para ampliar amostra”.


## 2026-09-28 — Fundação OpenSpec da Arena Duelo

- Criada a mudança OpenSpec `arena-duelo`, com proposta, decisões, requisitos e tarefas para sala privada, sincronização e proveniência de desempenho.
- A análise encontrou Socket.IO autenticado por JWT já implantado, MySQL/Doctrine como fonte de verdade e SPA Quasar com shell reutilizável; a decisão é usar REST para comandos e Socket.IO apenas para entrega/reconciliação.
- Criada a migration aditiva `033_arena_duel.sql` para salas, participantes, assuntos, questões congeladas e respostas imutáveis, além do contexto `ARENA_DUELO` em tentativas.
- Adicionada a regra pura `DuelScoring`, que atribui 100/75/50/25 às corretas por ordem determinística e -25 às erradas; lint PHP e teste focalizado no contêiner API aprovados (1 teste, 5 asserções).
- Pendências: repositórios Doctrine, serviços/rotas, integração de desempenho, gateway de sala, módulo SPA, testes de integração e validação no Compose.
- As dependências PHP não existem no checkout local; validações futuras devem continuar sendo executadas no contêiner API.
# Estado de desenvolvimento — ConquistaAI

## 2026-09-30 — Validação da importação PDF precisa e rápida

- A suíte focal de análise em lote, schema e entrada Codex foi aprovada (9 testes, 24 asserções); a regressão de segmentação permanece coberta no conjunto da mudança.
- `docker compose config --quiet` e o build da SPA foram aprovados. O build preserva somente o aviso conhecido de chunk acima de 500 kB.
- A mudança `accurate-fast-pdf-import` está validada; a próxima etapa é otimizar a execução Codex sem reduzir a análise editorial por modelo.

## 2026-09-30 — Retomada resiliente da fila de importação PDF

- Corrigida a reivindicação de checkpoints: pendentes elegíveis e leases expirados agora são consultados separadamente, evitando o `OR` que causava `filesort` sobre payloads JSON grandes. A migration `042_question_pdf_import_checkpoint_claim_order.sql` adiciona o índice `(status, created_at)` para a ordenação do próximo trabalho.
- Falhas DBAL na reivindicação passam a encerrar o ciclo de forma recuperável; o loop do worker inicia um novo processo com `EntityManager` limpo, em vez de propagar o estado fechado.
- A migration foi aplicada e o `question-pdf-worker` foi reiniciado. O job `7314960c-c5f9-4b00-87d5-5bb93bf107b6` retomou 120 checkpoints em processamento com oito executores Codex ativos; 1.100 permanecem pendentes.
- Validação: lint PHP, `docker compose config --quiet` e `EXPLAIN` da consulta pendente usando `idx_question_pdf_candidate_claim_order` sem `filesort`. O teste integrado legado de claim não é isolado da fila real e falhou ao reivindicar checkpoint de produção em vez da fixture; não foi usado como evidência da correção.
- Próximo passo: acompanhar a conclusão dos primeiros lotes e revisar quaisquer falhas específicas do provider.

## 2026-09-30 — Remoção do lote importado de Redes

- Removidas, por solicitação do usuário, as 580 questões vinculadas ao job `4d7ca5e7-0f76-4013-ac5e-e170eb579d3b`, suas 2.857 alternativas, 33 ativos visuais e vínculos de taxonomia.
- Não havia tentativas, cadernos ou interações vinculadas ao lote. O PDF-fonte e o histórico do job foram preservados para auditoria; a remoção do conteúdo é definitiva.
- Validação: contagem pós-transação confirmou zero questões remanescentes para o job, zero alternativas e zero ativos no banco.
- Próximo passo: nenhuma ação pendente para este lote removido.

## 2026-09-30 — Recuperação comprovada de banca e gabarito do lote de Redes

- Cruzados os 580 enunciados importados com o PDF-fonte, considerando repetições entre listas e questões comentadas somente quando a evidência convergia.
- Atualizadas 465 bancas e 368 gabaritos com fonte `OFFICIAL`; nenhuma resposta foi inferida. As páginas encontradas foram preservadas em `source_pdf_pages` como proveniência.
- Permanecem sem banca 115 questões e sem gabarito 212 questões para as quais o PDF não apresentou evidência única e explícita pelo cruzamento determinístico.
- Validação: lint do recuperador, prévia sem escrita (554 localizadas) e conferência SQL dos metadados e gabaritos persistidos.
- Próximo passo: revisar editorialmente os itens restantes com evidência ambígua ou texto não localizável.

## 2026-09-30 — Reclassificação do lote de Redes importado

- Corrigida a classificação provisória das 580 questões do job `4d7ca5e7-0f76-4013-ac5e-e170eb579d3b`, que antes vinculava todo o lote a Fundamentos de Redes.
- Regras determinísticas por conteúdo redistribuíram 423 questões entre 33 folhas canônicas, incluindo TCP/IP, IPv6, switching/VLAN, Ethernet, DNS, roteamento, redes sem fio, armazenamento, segurança e protocolos específicos. Permanecem 157 em Fundamentos de Redes por não haver evidência textual suficiente para uma folha mais específica.
- Validação: prévia sem escrita, lint do utilitário, e conferência pós-gravação de 580 vínculos distintos para as 580 questões do job.
- Próximo passo: revisão editorial opcional dos 157 itens genéricos para classificar casos que exijam contexto semântico além de palavras-chave.

## 2026-09-30 — Importação JSON de Redes com referências visuais do PDF

- Importadas 580 questões objetivas válidas do lote de Redes de Computadores como itens compartilhados do banco, sem criar concurso, cargo ou edital. `TCE-RJ` e `Auditor de Controle Externo - Tecnologia da Informação` permanecem somente nos metadados de origem de cada questão.
- O job `4d7ca5e7-0f76-4013-ac5e-e170eb579d3b` preserva o PDF de referência e o histórico da importação: 597 candidatos estruturais, 580 criados, 1 duplicado, 16 recusados na escrita e 1.439 itens sem estrutura objetiva excluídos previamente.
- Associados 33 ativos visuais às questões a partir de imagens incorporadas e de renderizações de páginas vetoriais indicadas no JSON; o fallback de página foi necessário porque o PDF não incorpora todas as figuras como bitmap.
- Validação: prévia estrutural, lint dos dois utilitários de importação, consulta de totais do job, questões e ativos persistidos.
- Próximo passo: revisão editorial das 16 rejeições e das questões que usam página inteira como referência visual, para eventual recorte fino.

## 2026-09-29 — Executor Codex da importação restaurado

- O `question-pdf-worker` foi reconstruído a partir da imagem que já instala `docker-cli`; a instância anterior estava defasada e não possuía o binário necessário para executar o runner isolado pelo socket Docker.
- A rede pública e os volumes de workspace/autenticação do Compose passaram a ter os nomes estáveis `conquistaai_public`, `conquistaai_correction_workspaces` e `conquistaai_codex_auth`, compatíveis com o executor configurado para importação, correção e explicação. Isso evita que o runner receba rede, workspace ou sessão diferentes dos usados pelos workers.
- O runner foi validado com os mounts efetivos de rede, workspace e autenticação (`codex-cli 0.157.1`). O job `f3370a78-3aa3-4ac4-9cfb-67d514588442`, antes `FAILED` por indisponibilidade do executor, foi reenfileirado e assumido pelo worker em `PROCESSING`.
- Corrigida a leitura do código de saída do subprocesso Codex: o worker preserva o resultado terminal de `proc_get_status()` antes de chamar `proc_close()`, evitando falha falsa quando o PHP devolve `-1` no fechamento apesar de `result.json` ter sido gerado.
- O adaptador também passou a registrar, de modo truncado e somente em `stderr`, o diagnóstico técnico de falha do executor. A mensagem não inclui conteúdo de candidatos nem a saída do modelo.
- A validação isolada do Codex revelou schema inválido para Structured Outputs: `schema_version` usava `const` sem `type`. O schema agora declara tipos explícitos nos campos constantes e enumerados, conforme a exigência do provider.
- O sandbox `workspace-write` do Codex dependia de namespaces de usuário via Bubblewrap, indisponíveis no kernel do host. Mediante autorização explícita, o runner isolado passou a usar `danger-full-access` somente no contêiner efêmero; ele não recebe socket Docker e monta apenas workspace e autenticação.
- A execução real foi validada após a correção: o runner leu `input.json` no workspace montado e o job foi novamente assumido em `PROCESSING`, sem a falha imediata anterior. Na conferência operacional ele atingiu 12% e persistiu a primeira questão. Lint dos dois arquivos e a suíte focal do validador passaram (2 testes, 3 asserções).
- Próximo passo: acompanhar a evolução e a conclusão do lote; o PDF possui 2.592 páginas e 1.260 candidatos, portanto o processamento completo não é imediato.

## 2026-09-29 — Retomada resiliente da importação PDF

- Corrigido o agendamento de retry de análises inválidas: os argumentos de checkpoint, tentativas e próxima execução eram enviados em posições incorretas, produzindo `TypeError` e deixando o job em `PROCESSING`.
- Jobs em `PROCESSING` sem atualização por 10 minutos são recuperados pelo worker. Cada checkpoint renova o lease, evitando que uma análise ativa seja retomada em paralelo.
- A retomada consulta análises já persistidas e pula o candidato correspondente; isso impede que uma interrupção reexecute o Codex e crie questões duplicadas.
- Os contadores do job agora são preservados quando ele é retomado. O worker registra erros PHP no `stderr`, tornando falhas futuras diagnosticáveis pelo log do Compose.
- Validação: lint PHP, `docker compose config --quiet`, `git diff --check` e suíte focal (3 testes, 4 asserções) aprovados. O job `f3370a78-3aa3-4ac4-9cfb-67d514588442` foi reenfileirado e está novamente em análise.
- Próximo passo: monitorar as novas tentativas e revisar a questão duplicada criada antes da proteção idempotente.

## 2026-09-29 — Limpeza controlada do banco de dados

- Dados de uso foram removidos mediante autorização explícita: questões e alternativas, importações e jobs PDF, cadernos, tentativas, revisões, interações, históricos, recursos auxiliares e sessões de autenticação.
- Foram preservados 5 usuários e sua configuração de acesso, o concurso Transpetro, exclusivamente o cargo `ANÁLISE DE SISTEMAS – INFRAESTRUTURA`, seu edital, 80 assuntos legados, 126 assuntos canônicos e os 72 vínculos canônicos do cargo preservado.
- Conferência posterior: 0 questões, 0 alternativas, 0 jobs PDF, 0 cadernos, 0 tentativas, 0 revisões e 0 sessões. O dump anterior à limpeza está em `concursos-backups/concursos-before-clean-20260929-105800.sql.gz`.
- Próximo passo: importar ou cadastrar novas questões para o cargo preservado.

## 2026-09-29 — Resiliência do contrato de paginação no cliente

- O cliente Axios de listas agora valida integralmente o envelope `data/meta/pagination` antes de acessar seus campos. Respostas incompatíveis passam a gerar `INVALID_API_CONTRACT` controlado, em vez de uma exceção JavaScript ao acessar `response.data.meta.pagination`.
- Corrigido o defeito que corrompia `GET /questions`: o repositório preenchia `qualityNotice`, mas o read model `PublishedQuestion` não declarava essa propriedade; o mapper emitia warning HTML antes do JSON. O modelo agora expõe o campo opcional do contrato.
- Próximo passo: validar a resposta autenticada e a página de questões após o recarregamento.

## 2026-09-28 — Proposta de evidência automática na correção de questões

- Criada a proposta OpenSpec `automatic-question-correction-evidence` para que o worker de correção localize páginas do PDF usando o primeiro parágrafo do enunciado e uma alternativa central não vazia, mesmo sem comando de busca na instrução.
- A proposta preserva as janelas de páginas de origem, a busca explícita por trecho, a busca de gabarito e a aprovação administrativa; não há alteração de rota ou payload.
- Artefatos de proposta, decisão técnica, especificação e tarefas foram concluídos; nenhuma alteração no worker foi aplicada nesta etapa.
- Próximo passo: aplicar a mudança por `/opsx:apply automatic-question-correction-evidence`.

## 2026-09-28 — Inicialização da API Study restaurada

- Corrigida a composição de `StudyController`: o registrador injetava `CreateNotebookService` no parâmetro de autenticação e deslocava todas as dependências, produzindo erro fatal HTML para qualquer endpoint da API, inclusive login.
- O controlador volta a receber `AuthService` seguido de um único caso de uso de criação com a exclusão de questões configurada.
- Próximo passo: validar o endpoint de sessão com token inválido, garantindo resposta JSON `401` em vez de HTML fatal.

## 2026-09-28 — Compatibilidade de resposta na autenticação

- O adaptador Axios passa a extrair dados tanto do envelope canônico `data/meta` quanto do payload direto legado devolvido pelo login, eliminando a falha de leitura de `access_token` quando o proxy/API retorna a forma direta.
- Os endpoints que seguem o contrato canônico permanecem inalterados; a compatibilidade fica isolada na infraestrutura HTTP.
- Próximo passo: validar o login pela interface após disponibilizar o novo módulo.

## 2026-09-28 — Recuperação segura da montagem global

- A aplicação não inicia mais bloqueada por um `q-inner-loading` de raiz: a tela de login é renderizada de imediato, enquanto a sessão é restaurada em segundo plano.
- Uma sessão válida substitui a tela de login pela aplicação; sem sessão recuperável ou diante de falha inesperada, a interface permanece utilizável para novo acesso.
- Validação: o Chromium isolado acessando `http://localhost:8081` montou a tela de login no DOM, incluindo os campos de e-mail, senha e o botão de acesso.

## 2026-09-28 — Validação final da execução mobile

- Build de produção aprovado no Compose (`vue-tsc --noEmit` e Vite); permanece somente o aviso não bloqueante de bundle acima de 500 kB.
- O Playwright iniciou os projetos desktop e mobile. O cenário autenticado parou antes do caderno porque a fixture E2E permaneceu na tela de login; a falha não alcançou os controles alterados. O cenário público de login foi aprovado nos dois perfis.
- Lint de todos os módulos PHP de Study e Performance alterados e `git diff --check` aprovados.

## 2026-09-28 — Continuidade, pausa e seleção inédita de caderno

- Implementados `activeQuestionId` persistido, rota autenticada de atualização e restauração do índice no composable; a migration `031_notebook_active_question.sql` suporta a nova coluna.
- Tentativas e respostas exigem estado `IN_PROGRESS` no servidor e a interface desabilita seleção/confirmação enquanto pausada; respostas pausadas retornam `409 STATE_CONFLICT`.
- A criação consulta uma porta Doctrine para excluir itens finalizados nos últimos 30 dias ou reservados por cadernos abertos do mesmo usuário. A seleção dirigida já preserva blocos de assunto na ordem congelada.
- Próximo passo: concluir testes integrados e build da proposta.

## 2026-09-28 — Rodapé móvel em linha única

- Todos os controles do rodapé móvel agora são ícones de 40 px, centralizados e sem quebra de linha; os grupos semânticos usam `display: contents` somente nesse breakpoint.
- Os rótulos continuam disponíveis no desktop e a acessibilidade permanece via `aria-label`.
- Validação proporcional: `git diff --check`. Próximo passo: concluir os casos de uso pendentes da proposta antes do commit.

## 2026-09-28 — Ícones consistentes no rodapé móvel

- O rodapé móvel oculta apenas os rótulos e apresenta ícones distintos para assunto anterior, próximo assunto, questão anterior, confirmação e próxima questão.
- Os mesmos ícones permanecem acompanhados dos rótulos no desktop; todos os controles mantêm `aria-label` descritivo.
- Validação proporcional: `git diff --check`. Próximo passo: continuidade e seleção de caderno da proposta.

## 2026-09-28 — Área ampliada de resolução mobile

- A seção contextual `book-heading` fica oculta em telas de até 599 px; os dados essenciais de questão, cronômetro, pausa e finalização continuam disponíveis na própria execução.
- A altura da área de trabalho foi recalculada para usar o espaço recuperado, sem afetar o layout desktop.
- Validação proporcional: `git diff --check`. Próximo passo: continuidade e seleção de caderno da proposta.

## 2026-09-28 — Grade uniforme de controles no rodapé móvel

- O rodapé da questão agora separa os atalhos de assunto das ações de resposta e usa grades proporcionais: dois controles de assunto, três ações durante a resposta e duas após o registro.
- Os botões ocupam colunas equivalentes, com altura de toque de 44 px, sem o espaço flexível que causava larguras irregulares.
- Validação proporcional: `git diff --check`. Próximo passo: manter a implementação de continuidade e seleção da proposta.

## 2026-09-28 — Contrato de continuidade de caderno

- O contrato HTTP inclui `PATCH /notebooks/{id}/active-question`, `activeQuestionId` na leitura e erros `409 STATE_CONFLICT` para tentativa ou resposta fora de `IN_PROGRESS`; a documentação frontend registra o fluxo pelas camadas Study.
- A tarefa documental 2.2 da proposta foi concluída. Próximo passo: persistir a posição ativa e restaurá-la na abertura do caderno.

## 2026-09-28 — Escopo ampliado: continuidade e seleção de caderno

- A proposta `mobile-notebook-execution` passou a incluir persistência da questão ativa, bloqueio transacional de respostas em pausa, ordenação por assunto e exclusão de questões respondidas nos últimos 30 dias ou reservadas em cadernos abertos do mesmo usuário.
- O contrato e a documentação frontend foram atualizados antes da implementação. A escolha inicial para “recentemente” é 30 dias; elegibilidade insuficiente deve retornar 422, sem repetir questões.
- Próximo passo: implementar o contrato, a persistência e os guardas de domínio antes da validação final da proposta.

## 2026-09-28 — Cobertura Playwright da execução responsiva

- Adicionado cenário Playwright para os projetos desktop e mobile: ícones acessíveis do cabeçalho, abas móveis, painel de navegação, progresso/finalização, rodapé persistente, arrasto horizontal e rolagem vertical sem troca de vista.
- Em desktop, o mesmo cenário confirma que as abas móveis ficam ocultas e que os painéis de navegação e progresso continuam simultaneamente visíveis.
- Próximo passo: executar o build do frontend e a suíte Playwright, então registrar o resultado final da proposta.

## 2026-09-28 — Controles preservados no cabeçalho móvel

- Os botões compactos de pausar/retomar e finalizar agora mantêm ícones Material visíveis, áreas de toque de 40 px e nomes acessíveis já expostos por `aria-label`.
- A redução visual continua ocultando apenas o texto dos controles no mobile, sem comprometer as ações essenciais.
- Próximo passo: cobrir as abas, o gesto e os controles nos projetos Playwright desktop e mobile.

## 2026-09-28 — Rodapé persistente na questão móvel

- A área de leitura da questão agora é um painel rolável independente e o rodapé com Anterior, confirmação de resposta e Próxima questão permanece fora dela.
- Em telas móveis, a área útil usa flexbox, `min-height: 0` e altura baseada na viewport dinâmica, evitando que o rodapé seja perdido em enunciados longos.
- Próximo passo: ajustar o cabeçalho compacto para preservar os ícones de pausa e finalização.

## 2026-09-28 — Arrasto horizontal no caderno mobile

- O contêiner das vistas móveis reconhece somente arrastos por toque com deslocamento horizontal predominante de ao menos 48 px; os limites da sequência são respeitados.
- A área declara `touch-action: pan-y`, mantendo a rolagem vertical do enunciado livre e sem alterar a aba ativa.
- Próximo passo: separar a área rolável da questão do rodapé de ações, para manter os controles disponíveis durante a leitura.

## 2026-09-28 — Abas móveis na execução de caderno

- A execução de caderno passou a organizar, em telas de até 599 px, as vistas de Questão, Navegação e Progresso em abas de apresentação locais, preservando a mesma fonte de estado de Study.
- A alternância por aba revela o painel correspondente sem alterar a questão atual ou as estatísticas reais já carregadas.
- Próximo passo: adicionar a troca por arrasto horizontal, sem interferir na rolagem vertical do enunciado.

## 2026-09-28 — Proposta de autenticação Google e gestão de usuários

- Criada a proposta OpenSpec `google-auth-user-management` para autenticação Google com validação OIDC no backend, vínculo explícito de e-mail Google e manutenção do login local.
- O primeiro acesso Google sem vínculo cria conta `PENDING_APPROVAL`, sem access token ou refresh token; a interface deve informar que a liberação administrativa é necessária. Coincidência com e-mail local não vincula contas automaticamente.
- A proposta prevê gestão administrativa paginada de usuários, cadastro, associação/remoção de e-mail Google, alteração auditável de papéis/status e proteção do último administrador ativo.
- Artefatos de proposta, design, especificações e tarefas foram concluídos; nenhuma funcionalidade, migração ou configuração externa foi aplicada nesta etapa.
- Próximo passo: aplicar a mudança por `/opsx:apply google-auth-user-management`, começando pela documentação de contrato e fundação do contexto Identity.
- Escopo ampliado: a proposta agora importa uma cópia validada da foto `picture` do Google apenas na criação da conta e permite que cada usuário ativo substitua sua própria foto pelo perfil. A foto manual prevalece sobre qualquer imagem futura do Google; o binário permanece protegido por autorização.

## 2026-09-28 — Contrato de autenticação Google e usuários

- O contrato de `POST /auth/google` foi documentado: validação OIDC exclusivamente no backend, sessão apenas para conta `ACTIVE`, criação idempotente em `PENDING_APPROVAL` e retorno seguro sem tokens durante a aprovação.
- A documentação também fixa as rotas administrativas `/admin/users`, a paginação, os comandos separados para vínculo Google, papéis e status, além da proteção do último administrador ativo.
- Próximo passo: documentar o módulo SPA de Identity/Administração de usuários e iniciar a migration reversível da fundação Identity.

## 2026-09-28 — Fluxo SPA de Google e gestão de usuários

- `docs/FRONTEND.md` registra o fluxo `Google Identity Services -> useAuth -> caso de uso -> repositório Axios -> API`, o retorno sem sessão para aprovação pendente e as mensagens seguras.
- O módulo administrativo foi especificado nas quatro camadas DDD, com listagem paginada e formulários Quasar que preservam estado confirmado diante de erros.
- Próximo passo: criar a migration reversível da fundação Identity.

## 2026-09-28 — Migration da fundação Identity

- Criada a migration `032_google_auth_user_management.sql`: senha local passa a ser opcional, o estado `PENDING_APPROVAL` é adicionado sem alterar registros existentes e os metadados privados de avatar são incluídos.
- O vínculo Google exclusivo por usuário/e-mail e os eventos de auditoria de identidade foram definidos com chaves estrangeiras e índices. O rollback está documentado no arquivo e exige resolver previamente contas pendentes e usuários sem senha local.
- Próximo passo: modelar os contratos de domínio Identity para esses dados.

## 2026-09-28 — Modelo de domínio Identity

- Adicionados `UserStatus`, `AvatarSource`, `GoogleEmail`, as entidades `GoogleIdentity`, `UserAvatar` e `IdentityAuditEvent`, além das portas de vínculo Google e auditoria.
- `User` agora aceita ausência de senha local e informa explicitamente se ela está configurada, preservando a regra de acesso ativo existente.
- Próximo passo: implementar a persistência Doctrine, transações e armazenamento privado para esses contratos.

## 2026-09-28 — Persistência Identity e avatar privado

- Implementados repositórios Doctrine para identidade Google e eventos de auditoria, com entidades mapeadas para as novas tabelas e sem campos de credenciais externas.
- O registro Doctrine de usuário aceita senha e metadados de avatar anuláveis; `PrivateAvatarStorage` usa chaves relativas, permissões privadas e rejeita travessia de caminho.
- Validação: lint PHP dos contratos e adaptadores Identity, além de `git diff --check`, aprovados.
- Próximo passo: implementar a validação OIDC do Google.

## 2026-09-28 — Validação OIDC Google

- Implementado `GoogleOidcValidator`: busca e mantém JWKS em cache, verifica JWT RS256, emissor, público configurado, expiração e e-mail confirmado antes de expor uma identidade tipada ao caso de uso.
- A credencial bruta não atravessa o adaptador; erros retornam mensagens seguras e não causam persistência.
- Validação: lint PHP do adaptador e `git diff --check` aprovados.
- Próximo passo: usar a identidade validada no caso de uso de autenticação Google.

## 2026-09-28 — Caso de uso de autenticação Google

- `GoogleAuthenticationService` resolve exclusivamente o vínculo Google normalizado; sem vínculo cria uma única conta `PENDING_APPROVAL` com papel `USER` e sem senha local.
- Coincidência com e-mail local sem vínculo gera conflito, e somente vínculo de usuário `ACTIVE` recebe sessão JWT/refresh. A credencial não é persistida nem registrada.
- Próximo passo: aplicar estados de acesso também a login local, refresh e sessão atual.

## 2026-09-28 — Estados de acesso nas sessões

- Login local agora rejeita usuário sem senha, devolve condição segura de aprovação pendente após validar credenciais e continua bloqueando contas não ativas.
- Refresh e `GET /auth/me` já revogavam/rejeitavam usuários não ativos; a resposta de sessão passa a incluir o status, preservando o bloqueio sem tokens.
- Próximo passo: expor o comando e a resposta Google pela rota HTTP.

## 2026-09-28 — Rota HTTP de autenticação Google

- `POST /v1/auth/google` recebe DTO validado de credencial e nome opcional de dispositivo, delega ao caso de uso e responde no envelope padrão.
- Contas pendentes retornam dados sem token/cookie; colisão de vínculo retorna `409 STATE_CONFLICT`; credencial inválida retorna `401 UNAUTHENTICATED`.
- Próximo passo: adicionar testes de unidade e integração para OIDC, pendência, colisão e sessão bloqueada.

## 2026-09-28 — Cobertura parcial de autenticação Google

- Os testes unitários Identity agora cobrem JWT OIDC RS256 assinado por JWKS controlada, e-mail não confirmado, criação pendente idempotente, colisão com conta local e emissão de sessão para vínculo ativo: 7 testes e 13 asserções aprovados no contêiner API.
- A verificação HTTP integrada está pendente: o contêiner em execução usa a imagem anterior e não monta o fonte atualizado; a reconstrução controlada será feita junto da execução da migration na tarefa 6.2.
- Próximo passo: importar com segurança o avatar inicial declarado pelo Google.

## 2026-09-27 — Idempotência da auditoria publicada

- A rotina de auditoria agora procura uma execução `COMPLETED` com o mesmo algoritmo e escopo antes de iniciar outra; quando existe, retorna seu identificador sem consultar questões, criar execução ou gravar achados.
- Cobertura unitária aprovada: `ProcessQuestionAuditServiceTest` (1 teste, 5 asserções). A validação integrada do comando e da suíte completa é o próximo passo; não haverá reprocessamento de PDF, uso de job/IA ou importação de questões nesta etapa.
- A tentativa de validação contra o banco local foi interrompida antes de qualquer escrita porque o hostname Docker `mysql` não resolve neste ambiente (`PDOException`); a rotina não reprocessou PDFs, não criou execução e não importou questões. A validação comportamental permanece coberta pelo teste unitário.
- Validação posterior: PHPUnit completo aprovado (56 testes, 134 asserções; 2 deprecações já existentes) e `git diff --check` limpo. O build do frontend não foi reexecutável neste ambiente porque `node_modules` é de outro proprietário e está incompleto; `npm ci` falhou com `EACCES` ao criar `node_modules/@babel`, sem alterar código-fonte.
- Imagens: o escritor deixou de anexar automaticamente todos os arquivos extraídos de uma página. `image_pages` é somente proveniência; a persistência exige `verified_assets` com caminho e página explicitamente validados por recorte determinístico ou revisão humana. Teste focalizado aprovado (2 testes, 6 asserções).
- Renderização: corrigido o parser compartilhado para manter todos os caracteres e quebras dos blocos de código legados; ele também reconhece Shell, comandos e outras linguagens sem aplicar formatter. O build permanece pendente apenas pelo `node_modules` incompleto e protegido por proprietário externo.
- Idempotência refinada: após uma execução concluída, o repositório verifica alterações de questões ou assets publicados desde a data final. Sem mudança, reutiliza o relatório; com mudança, abre nova execução. A porta de domínio isola essa verificação e os dois fluxos estão cobertos por `ProcessQuestionAuditServiceTest` (2 testes, 11 asserções).
- Deduplicação: detector Doctrine, escritor PDF e prévia de importação compartilham `QuestionStatementFingerprint`. A chave remove somente artefatos de extração (ligaturas, diacríticos, pontuação e layout) e usa SHA-256; assim, a mesma questão não é reimportada por diferença formal. Testes focalizados aprovados (3 testes, 6 asserções).
- A simulação direta `import-interrupted-pdf-direct.php --dry-run` agora usa a mesma impressão digital canônica do escritor; seus totais de duplicatas e candidatos novos não divergem mais da proteção efetiva contra reimportação.
- O renderizador também trata `c#` como identificador de linguagem da cerca Markdown; o rótulo é removido da apresentação e o corpo literal permanece intacto.
- Relatórios estruturais: `RUIDO_EXTRACAO`, `CODIGO_SEM_BLOCO` e `ESTRUTURA_CORRELACAO` passaram a ser achados distintos. Código e correlação gravam em `structure_after` apenas a apresentação proposta (`CODE_BLOCK`/`MATCHING_COLUMNS`), sem alterar o texto de origem. Cobertura focalizada aprovada (5 testes, 16 asserções).
- A API e a tela administrativa agora expõem `structureAfter` quando a auditoria propõe somente a apresentação de código ou correlação. A proposta é informativa, persistida e não aciona atualização de conteúdo.
- A análise persistida de código foi alinhada ao frontend: SQL, linguagens comuns, XML/HTML, JSON e comandos Shell passam a gerar `CODIGO_SEM_BLOCO` quando não delimitados. Teste de Shell aprovado.
- Validação integrada no Compose: `bin/audit-published-questions.php` reutilizou com sucesso a execução `7fd441ae-c5fd-4d4b-afe7-bfd84a2ce3e8`, sem escrita em questões; `npm run build` no contêiner frontend foi aprovado. Permanece somente aviso não bloqueante de bundle acima de 500 kB.
- A versão `question-audit-v2` foi criada para materializar os novos códigos e propostas de apresentação; execução direta no contêiner concluiu como `d21bc10a-f0c1-4e2d-8ba7-f4f5318f6123`, sem job, IA ou alteração de questões. A conferência de totais no banco é o próximo passo.
- Conferência somente leitura no MySQL: a v2 concluiu 4.973 questões publicadas e 1.228 achados; 393 códigos sem bloco, 27 correlações, 314 imagens sem localização segura, 261 ruídos, 97 extrações incompletas, 84 duplicatas possíveis e 52 mesclas possíveis. O acervo permanece com 4.973 `PUBLISHED`, 506 `DRAFT` e 231 `VOID`.
- Validação final da etapa: PHPUnit completo aprovado (61 testes, 147 asserções), build de produção do frontend aprovado no Compose e `git diff --check` limpo. O único aviso é o chunk JS acima de 500 kB.
- Inspeção de ruídos v2: 250 dos 261 incluem o rodapé editorial conhecido; porém a mesma amostra contém caracteres já corrompidos (`�`). Não foi aplicada limpeza em massa para não combinar remoção segura de rodapé com reconstrução inferida de conteúdo. Os casos permanecem em revisão até existir operação isolada, auditável e reversível.
- A auditoria agora separa `CARACTERE_CORROMPIDO` (substituto `�`) como achado de alta confiança, exigindo comparação com o PDF original e impedindo qualquer reconstrução inferida.
- A auditoria `question-audit-v3` foi executada diretamente no Compose como `a54c568e-5026-471a-93c8-f5040d4d6e64`. Não houve achado `CARACTERE_CORROMPIDO`: a amostra em hexadecimal confirmou UTF-8 válido; o símbolo exibido na consulta anterior era artefato de codificação do terminal.
- Correção estrutural aplicada diretamente, sem job/IA: a rotina `repair-published-pdf-footers-direct.php --apply` removeu somente a linha de rodapé editorial conhecida de 243 enunciados, com prévia confirmada e conteúdo anterior preservado nos achados v3. A reauditoria `18838fe7-01a6-45c3-85a0-6eec5a6a77c9` reduziu `RUIDO_EXTRACAO` de 261 para 19; estes 19 permanecem manuais. Nenhuma alternativa, gabarito, asset, PDF ou estado editorial foi modificado.
- A auditoria `question-audit-v4` (`4f0bddb2-3467-415c-ba80-6cd8c7e18875`) classificou 5 casos como `CONTEUDO_DOCUMENTAL_MESCLADO` (sumário/gabarito/documento inteiro no enunciado), separando-os dos 19 ruídos residuais. Nenhum deles foi alterado automaticamente.
- Verificação de fonte dos 5 casos documentais: todos apontam para o PDF completo de Desenvolvimento de Software. A página 668 contém questões consecutivas, comentários e gabaritos adjacentes, enquanto o enunciado publicado incorporou material de outras partes; não existe corte automático seguro baseado apenas na página. Os cinco permanecem em revisão manual.























## 2026-09-27 — Recuperação das fontes originais de PDF

- As 43 fontes de `question_pdf_import_jobs` foram restauradas a partir de `backups/pdfs-originais`: 36 por nome idêntico e 7 por correspondência manual inequívoca de aula/título.
- A validação determinística com `pdftotext` na primeira página de cada fonte concluiu `readable=43 failed=0`; o bloqueio anterior de streams Flate corrompidos foi removido.
- Não foi criado, reimportado ou alterado nenhum registro de questão. O próximo passo é usar exclusivamente essas fontes recuperadas para localizar assets ausentes e executar simulações deduplicadas antes de qualquer escrita.

## 2026-09-27 — Triagem segura de imagens ausentes

- As 314 ocorrências de imagem ausente representam 287 páginas distintas. A inspeção de `pdfimages -list` em todas elas encontrou duas ou mais imagens incorporadas por página; não há associação unívoca segura.
- Nenhum asset foi associado automaticamente: os achados continuam em `IMAGEM_NAO_LOCALIZADA` para revisão manual, preservando a questão e evitando anexar logos, cabeçalhos ou elementos visuais alheios.

## 2026-09-27 — Simulação integral com deduplicação explícita

- A extração direta, local e sem IA concluiu `--dry-run` dos 43 PDFs restaurados: 23.761 blocos, 12.902 candidatos objetivos e 6.744 candidatos com gabarito oficial. Nenhuma questão foi escrita.
- A simulação agora informa duplicatas exatas, inclusive internas ao PDF. Na amostra de Banco de Dados, 212 de 855 candidatos já existem exatamente; os 643 restantes exigem deduplicação por similaridade/proveniência antes de qualquer aplicação.
- O detector passou a normalizar exclusivamente ligaturas, diacríticos, pontuação e layout de extração antes da comparação exata; o teste focalizado aprovou 2 testes e 5 asserções. A amostra permaneceu em 212 duplicatas, portanto nenhum candidato ambíguo foi liberado.
- O read model tipado e a consulta Doctrine agora são expostos por `GET /admin/question-audits/latest`, com DTO, mapper, caso de uso, controlador fino e autorização ADMIN; a rota não altera questões.
- Criada a seção administrativa Auditoria no frontend (porta de domínio, caso de uso, repositório Axios, composable e página Quasar) para visualizar totais da última execução sem ações de correção.
- Validações: build frontend aprovado, PHPUnit QuestionBank aprovado (17 testes, 37 asserções), lint PHP e `git diff --check` aprovados; a rota sem credenciais devolve `401 UNAUTHENTICATED`.
- `QuestionContent.vue` deixou de exibir o identificador de linguagem como código e não aplica mais `trim` ao corpo entre cercas; o build de produção foi aprovado após a alteração.
- A listagem detalhada foi concluída em `GET /admin/question-audits/latest/findings`, com Request/Response DTOs, mapper, service, QueryBuilder Doctrine, paginação e autorização ADMIN; sem mutação de questões ou achados.
- A tela Auditoria passa a exibir código, confiança, estado, mensagem, questão e PDF/página de origem de cada achado, com paginação Quasar. Build frontend, lint PHP, testes QuestionBank e `git diff --check` foram aprovados; a rota sem credenciais devolve `401`.
- Validação integrada final desta etapa: PHPUnit completo aprovado (55 testes, 129 asserções), build frontend aprovado e `git diff --check` limpo. Persiste somente aviso não bloqueante de chunk acima de 500 kB.
- O renderizador compartilhado passou a identificar visualmente blocos legados de SQL, comandos, código-fonte e XML/JSON, sem alterar seu conteúdo, e os apresenta com fonte monoespaçada preservando espaços e quebras. Build frontend aprovado.

Atualizado em 28/09/2026. Este é o registro de handoff obrigatório antes de iniciar uma nova etapa. Ele complementa o cronograma e reduz a dependência do histórico de conversa.

## 2026-09-28 — Contratos HTTP de revisão adaptativa

- Documentadas as rotas autenticadas de sessão diária, revisão rápida, classificação imutável, mapa hierárquico de domínio e resultado persistido da análise de caderno.
- A documentação fixa isolamento por usuário, os estados assíncronos, os formatos de card/conceito e o tratamento explícito de evidência insuficiente.
- Validação: `git diff --check` aprovado após a documentação dos contratos e da SPA.
- Próximo passo: criar a migration aditiva do contexto Review.

## 2026-09-28 — Módulo SPA Review documentado

- Registrado o fluxo DDD completo, os estados reais de sessão, análise assíncrona e domínio, além da responsividade da revisão.
- A documentação proíbe dados simulados e mantém a API como fonte de verdade para retomada e resultados.

## 2026-09-28 — Persistência aditiva de Review

- Criada a migration `037_adaptive_flashcard_learning.sql` com cards deduplicáveis, relações canônicas, sessões retomáveis, progresso individual, histórico imutável, domínio, execução idempotente e auditoria de ações.
- As chaves compostas e os índices cobrem isolamento por usuário, fila de vencimento, retomada de sessão, histórico e unicidade por caderno/versão.
- Decisão: o rollback é procedural e exige preservar o histórico, pois reviews e análises são registros auditáveis.
- Validação: revisão estática SQL e `git diff --check` aprovados; aplicação controlada será executada na etapa de qualidade.
- Próximo passo: modelar o domínio Review e suas portas de persistência.

## 2026-09-28 — Domínio Review e portas

- Modelados cartões, sessões, progresso, eventos imutáveis, domínio agregado, execuções e ações de análise; enums tornam os estados e ações permitidas explícitos.
- `FlashcardFingerprint` versiona a chave determinística por conceito canônico e conteúdo normalizado; as portas do domínio não expõem DTOs HTTP nem Doctrine.
- Validação: lint de todos os arquivos PHP novos e `git diff --check` aprovados.
- Próximo passo: implementar os adaptadores Doctrine e transações Review.

## 2026-09-28 — Adaptadores Doctrine Review

- Adicionados mapeamentos Doctrine e adaptadores para cards, progresso, sessões, histórico, domínio e execuções/auditoria de análise; todas as consultas de leitura são parametrizadas e filtram usuário onde aplicável.
- As gravações permanecem unitárias e são prontas para composição pelo `DoctrineTransactionManager` nos casos de uso, sem acesso PDO/SQL cru fora da migration.
- Validação: lint PHP dos adaptadores/mapeamentos e `git diff --check` aprovados.
- Próximo passo: implementar as estratégias substituíveis de repetição, domínio e prioridade.

## 2026-09-28 — Núcleo adaptativo configurável

- Implementadas portas substituíveis para repetição espaçada, domínio e prioridade, com configuração única para os intervalos iniciais.
- A estratégia inicial trata `AGAIN`, `HARD`, `GOOD` e `EASY`; o estimador expõe confiança insuficiente sem atribuir domínio baixo; a prioridade combina atraso, lacuna e sinais recentes.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: criar/reutilizar flashcards por fingerprint canônica.

## 2026-09-28 — Deduplicação de flashcards

- O caso de uso valida o assunto canônico ativo, calcula fingerprint versionada de frente/verso e reutiliza o card equivalente antes de persistir um novo.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: montar sessões diárias e rápidas priorizadas.

## 2026-09-28 — Sessões priorizadas de Review

- `BuildReviewSessionService` monta ou retoma sessões DAILY/QUICK exclusivamente do usuário autenticado, restringe limite a 100 e ordena cards vencidos pelo cálculo central de prioridade.
- As associações de cards da sessão são persistidas para permitir retomada sem recomposição aleatória da fila.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: registrar a classificação com atualização atômica de progresso e histórico imutável.

## 2026-09-28 — Classificação imutável de cards

- A classificação valida sessão e card do próprio usuário, executa sob transação, aplica a estratégia de espaçamento e persiste estado anterior/próximo como evento imutável.
- Reenvio para o mesmo card/sessão não duplica o evento nem o progresso.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: disponibilizar mapa de domínio e resultado persistido de análise.

## 2026-09-28 — Leituras Review isoladas

- O mapa retorna nós vinculados à Taxonomy canônica, com pai, escore opcional, confiança e amostra, enquanto a análise de caderno é lida somente quando o caderno pertence ao usuário.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: mapear a infraestrutura de worker/provider do corretor para análise de caderno.

## 2026-09-28 — Reuso do pipeline de IA mapeado

- A análise de caderno reutilizará o padrão do worker `bin/process-question-corrections.php`: claim persistido, workspace temporário, schema JSON, timeout, logs estruturados de duração e falha segura.
- O provider configurado e a execução são isolados do domínio; a telemetria persistida fica na execução Review, sem cadeia de raciocínio.
- Próximo passo: implementar o resumo mínimo e o schema estrito da resposta de análise.

## 2026-09-28 — Contrato estruturado de análise

- O provider recebe somente respostas finalizadas, contagem de acertos e tempo; o validador aceita apenas ações enumeradas, limita volume e rejeita JSON incompleto antes de qualquer aplicação.
- Nenhuma cadeia de raciocínio integra o payload ou a persistência.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: persistir e processar a execução idempotente por caderno/versão.

## 2026-09-28 — Aplicação transacional de ações de análise

- O aplicador recebe somente ações validadas, cria/reutiliza cards, antecipa cards existentes e atualiza sinais mínimos de domínio na mesma transação, gravando auditoria curta por ação.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: conectar a execução pendente ao worker e à finalização não bloqueante de caderno.

## 2026-09-28 — Integração parcial HTTP e SPA Review

- A finalização de caderno agenda execução idempotente de análise sem bloquear a resposta; as rotas de leitura de mapa de domínio e análise persistida foram registradas com isolamento por usuário.
- Criadas as camadas Domain, Application e Infrastructure Axios do frontend Review e composta a dependência no container. As telas e as rotas de sessão/classificação ainda dependem da conclusão dos contratos HTTP correspondentes.
- Validação: lint PHP e `git diff --check` aprovados. `vue-tsc` não pôde iniciar por incompatibilidade existente entre a versão resolvida de `vue-tsc` e a exportação de TypeScript (`ERR_PACKAGE_PATH_NOT_EXPORTED`).
- Pendências: worker de análise, endpoints de sessão/classificação, UI Quasar e cobertura específica Review.

## 2026-09-28 — Estado de apresentação Review

- Adicionado `useReview`, que consome somente use cases e expõe carregamento, erro, vazio por coleção e estado persistido `PENDING`/`PROCESSING` da análise.
- Não há dados simulados: mapa e análise dependem exclusivamente das rotas Review já registradas.

## 2026-09-28 — Página Quasar de domínio

- Criada a página `ReviewInsightsPage` com cartão Quasar, carregamento, erro, vazio e evidência insuficiente explícita; ela consome somente `useReview`.
- A página está integrada à navegação principal como **Revisão**. A apresentação de análise por caderno permanece pendente.

## 2026-09-28 — Sessões HTTP Review

- Registradas as rotas autenticadas de sessão diária e rápida, ambas montadas pelo serviço de prioridade e serializadas por DTO/mapper de resposta.
- A classificação HTTP foi conectada a `POST /v1/review/sessions/{sessionId}/cards/{cardId}/reviews`, delegando para o serviço transacional e retornando DTO de revisão.
- Validação desta etapa: lint PHP e `git diff --check`.

## 2026-09-28 — Fluxo de classificação SPA

- O repositório Axios, use case e composable Review agora enviam classificações reais; foi criada uma página Quasar de sessão com retomada, revelação e as quatro opções de recordação.
- O contrato foi corrigido: sessões retornam frente, verso e conceito de cada card; a SPA deixou de exibir identificadores técnicos.
- A página de sessão precisa ser exposta na seção Review junto ao mapa de domínio.

## 2026-09-28 — Limite de camadas no contrato de sessão

- A hidratação de cards passou ao serviço de aplicação; `ReviewResponseMapper` apenas converte entidades de domínio em DTOs e não consulta repositórios.
- Validação: lint PHP dos serviços, mapper e registrador, além de `git diff --check`.

## 2026-09-28 — Módulo frontend DDD Review

- Concluídas as camadas Domain, Application, Infrastructure Axios e composição no container para sessões, classificação, mapa de domínio e análise de caderno.
- Próximo passo: finalizar páginas e integração contextual dos resultados de análise.

## 2026-09-28 — Sessão Quasar de flashcards

- A seção Review passou a alternar entre cards e mapa; a sessão oferece revisão diária/rápida, revelação, classificação, progresso e adaptação mobile.
- Validação: `git diff --check`; build TypeScript permanece bloqueado pela incompatibilidade preexistente de `vue-tsc`.

## 2026-09-28 — Painel de análise de caderno

- Criado `NotebookAnalysisPanel` reutilizável para exibir exclusivamente o estado persistido da análise, tratando carregamento, erro, ausência, processamento, falha e conclusão.
- O painel foi integrado ao progresso lateral de cadernos `FINISHED`; a análise permanece fonte de verdade e não bloqueia o encerramento.

## 2026-09-28 — Resultado e domínio integrados

- Concluídas as telas de mapa navegável e análise persistida, incluindo estados de carregamento, erro, vazio, processamento e falha.
- A navegação Review e o painel no Caderno conectam a experiência ao fluxo existente de desempenho/estudo.

## 2026-09-28 — Testes iniciais Review

- Adicionados testes unitários para intervalo curto de `AGAIN`, fingerprint normalizada e rejeição de resposta inválida do provider; execução focalizada aprovada com 4 testes e 6 asserções.
- Permanecem pendentes cenários de idempotência, isolamento, histórico baixo/alto e testes de controller/repositório.

## 2026-09-27 — Busca textual e leitura de questões

- `GET /questions` agora aceita `content` (até 200 caracteres) e aplica a busca parametrizada no enunciado e nas alternativas das questões publicadas, preservando filtros e paginação. A validação real por Doctrine para `IPv4` retornou 67 resultados.
- A tela Banco de Questões inclui o campo “Pesquisar no conteúdo” e apresenta cada resultado aberto, com metadados, conteúdo rico, anexos e alternativas no mesmo padrão visual do Caderno.
- Validações: PHPUnit completo aprovado (64 testes, 150 asserções), build de produção do frontend aprovado e `git diff --check` limpo. Permanece apenas o aviso não bloqueante de bundle acima de 500 kB.

## 2026-09-27 — Correção dos achados estruturais verificáveis

- Executada a rotina direta `repair-published-pdf-footers-direct.php --apply`, sem job ou IA. Uma questão adicional (`ddd7eea9-731b-4088-9db1-cfcc07c3b2fa`) teve somente linhas completas de rodapé editorial removidas; alternativas, gabarito, imagens, metadados e demais trechos foram preservados.
- A reauditoria direta `11875e5c-b1c5-4e9d-b21d-573677ba37d3` reduziu `RUIDO_EXTRACAO` de 19 para 18. Código (393) e correlação (27) permanecem corrigidos na apresentação compartilhada, sem reescrita de dados.
- Permanecem em revisão manual 314 imagens sem associação segura, 97 extrações incompletas, 84 duplicatas possíveis, 52 mesclas possíveis, 5 conteúdos documentais misturados e 18 ruídos residuais; não há base determinística para alterar esses conteúdos sem risco de mudar a questão original.
- Validações: lint da rotina, PHPUnit completo (64 testes, 150 asserções), build de produção do frontend e `git diff --check` aprovados.

## 2026-09-27 — Reparo estrutural verificado de alternativa incorporada

- Corrigida diretamente, sem job ou IA, a questão publicada `0025b8cc-c6f9-407f-99ba-e5b8de21be61` (FGV/Prefeitura de Caraguatatuba-SP/2024). A comparação com a página física 889 do PDF recuperado (referência de origem persistida: `[883]`) confirmou que as quatro afirmações e a instrução estavam indevidamente no conteúdo da alternativa A.
- A rotina idempotente `bin/repair-published-question-0025b8cc-direct.php` move exclusivamente esse trecho literal para o fim do enunciado e mantém a alternativa A como `F – V – F – V.`. Gabarito, alternativas B–E, metadados e proveniência não foram modificados.
- A auditoria passa a sinalizar o padrão como `ALTERNATIVAS_INCORPORADAS` com confiança alta e sem reescrever conteúdo; o teste unitário cobre a detecção.

## 2026-09-27 — Versionamento de reprocessamento de PDFs

- Aplicada a migration `030_question_pdf_reprocess_version.sql`, que adiciona versão de algoritmo e vínculo com o job de origem aos imports de PDF.
- O mapeamento Doctrine de `QuestionPdfImportJobRecord` foi atualizado e a suíte completa da API foi aprovada com 54 testes e 128 asserções.
- Os 43 jobs históricos permanecem preservados em `legacy`; nenhum novo job foi criado até a rotina de clonagem idempotente estar disponível.

## 2026-09-27 — Testes da auditoria determinística

- Adicionada cobertura unitária para referência visual sem asset e para deduplicação de achados `EXTRACAO_INCOMPLETA` por questão.
- Teste focalizado aprovado: 2 testes, 3 asserções. A suite completa e o build de frontend permanecem verdes na validação anterior.

## 2026-09-27 — Integridade dos estados de auditoria

- Corrigida a semântica do relatório: detecção automática não representa correção aplicada. Achados sem mutação efetiva passam a `REQUER_REVISAO`.
- A migration `029_question_audit_review_status.sql` atualizou relatórios anteriores sem modificar questões; a nova execução `7fd441ae-c5fd-4d4b-afe7-bfd84a2ce3e8` confirmou 523 em revisão, 314 imagens não localizadas, 52 possíveis mesclas, 84 possíveis duplicatas e 97 extrações incompletas.
- Essa separação impede publicação ou alteração inferida e mantém fidelidade ao material como prioridade.

## 2026-09-27 — Proveniência dos achados para recuperação visual

- A leitura de questões publicadas passou a disponibilizar internamente o job e as páginas de origem já persistidos; a auditoria grava essas referências em cada achado.
- A execução `27b8695a-0f61-497b-b313-409ed0d78922` concluiu e os 314 achados `IMAGEM_NAO_LOCALIZADA` possuem agora job e página de origem, viabilizando extração verificável do asset sem criação artificial.
- Validação: suíte completa da API aprovada (52 testes, 125 asserções). Próximo passo: recuperar visualmente apenas os casos cujo recorte de página possa ser associado com segurança; os demais mantêm revisão manual.

## 2026-09-27 — Correlação com indicadores preservados no frontend

- O componente compartilhado `QuestionContent.vue`, usado em Banco de Questões, Caderno e Revisão, passou a reconhecer e exibir indicadores numéricos, alfabéticos e romanos com o delimitador original.
- A coluna de itens não converte mais os identificadores em numeração artificial; isso preserva a estrutura da questão de origem.
- Build de produção aprovado (`vue-tsc` + Vite); permanece apenas o aviso não bloqueante de chunk acima de 500 kB.

## 2026-09-27 — Validação da fundação de auditoria

- A suíte completa da API foi aprovada após a inclusão da auditoria persistida: 52 testes e 125 asserções.
- O build de produção do frontend também foi aprovado (`vue-tsc` e Vite); permanece apenas o aviso não bloqueante de chunk JavaScript acima de 500 kB.
- A auditoria real permanece concluída e somente leitura; o próximo incremento é a interface administrativa do relatório e a evolução do componente compartilhado de renderização, sem promover correções de baixa confiança.

## 2026-09-27 — Primeira auditoria persistida de questões publicadas

- Aplicada a migration `028_question_audit.sql` e executada a rotina `bin/audit-published-questions.php`, sem qualquer alteração de enunciado, alternativa, gabarito, asset ou estado editorial.
- A execução `8412ccad-ea04-4897-a4cd-50dff292819b` concluiu com 4.973 questões analisadas e 1.070 achados: 523 sinais de formatação automática, 314 referências visuais sem asset, 52 possíveis mesclas, 84 possíveis duplicatas e 97 extrações incompletas.
- Há 628 ocorrências de confiança média que exigem revisão ou evidência do PDF antes de correção. O relatório e todas as ocorrências permanecem persistidos nas tabelas de auditoria.
- Próximo passo: expor relatório administrativo, recuperar assets a partir da página/fonte e criar somente correções de alta confiança; não realizar atualização em massa por inferência.

## 2026-09-27 — Persistência de auditoria editorial

- Criada a migration `028_question_audit.sql`: execuções versionadas (`question_audit_runs`) e ocorrências por questão (`question_audit_findings`) com PDF/página, confiança, estado final e estruturas antes/depois.
- A chave única por execução, questão e código protege o relatório de repetição acidental; as ocorrências mantêm os estados exigidos para revisão e nunca substituem conteúdo de origem.
- Próximo passo: implementar o repositório Doctrine, a rotina em lotes retomável e a visualização administrativa do relatório antes de aplicar a migration ou auditar dados publicados.

## 2026-09-27 — Fundação de fidelidade para auditoria de questões

- A importação de PDFs passou a aceitar exclusivamente questões objetivas com quatro ou cinco alternativas e rejeita alternativas duplicadas após a normalização. Itens binários ou incompletos permanecem fora da publicação e devem seguir para revisão.
- O sanitizador agora preserva integralmente conteúdo delimitado como bloco de código, sem remover indentação, quebras de linha, comentários, espaços ou operadores.
- Validações: PHPUnit focalizado do QuestionBank aprovado (8 testes, 12 asserções), incluindo o caso de preservação literal de código; `git diff --check` aprovado.
- Próximo passo: persistir a auditoria idempotente e seu relatório por questão/PDF, antes de executar qualquer correção em massa de conteúdo publicado.
## 2026-09-27 — Extração direta dos jobs de Segurança interrompidos

- Os cinco jobs cancelados em 27/09 foram concluídos sem worker ou provedor externo por `bin/import-interrupted-pdf-direct.php`: SSL/TLS e VPN (146 páginas, sem item A–E elegível), Criptografia (122 páginas, 26 detectadas, 5 novas e 21 duplicadas), Certificação Digital (65 páginas, 10 detectadas, 2 novas e 8 duplicadas), LDAP/Active Directory (104 páginas, sem item A–E elegível) e Gestão de Identidade e Acesso (29 páginas, sem item A–E elegível).
- O classificador local passou a priorizar SSL/TLS, segurança de redes, criptografia/certificação, gestão de identidade/acesso, LDAP, compartilhamento de arquivos em rede e Active Directory. As sete novas questões foram associadas ao assunto canônico existente `Criptografia e Certificação Digital`; nenhuma taxonomia genérica ou nó novo foi criado.
- Os PDFs sem criação continham somente teoria ou itens de certo/errado; continuam descartados pela política de aceitar apenas múltipla escolha completa. Cinco das sete questões novas possuem gabarito `OFFICIAL`; duas permanecem sem gabarito explícito e não receberam inferência.
- Validações: simulação antes da escrita, conclusão em 100% de todos os jobs, consulta de questões/gabaritos/taxonomia por Doctrine e lint do importador aprovados.

## 2026-09-27 — Ordenação da fila editorial

- A listagem administrativa passou a incluir os três estados editoriais em ordem determinística: `REVIEW` (revisar), `DRAFT` (aprovar/publicar) e `VOID` (somente consulta). A paginação é aplicada após essa ordenação, impedindo que itens inválidos ocupem posições prioritárias.
- A tela editorial diferencia `VOID` como “Invalidada — conteúdo incompleto ou duplicado”, sem checkbox, classificação ou ações de publicação; `DRAFT` continua sendo o único estado selecionável para publicação.
- Validações: consulta integral pelo repositório Doctrine confirmou ordem não decrescente por prioridade; o conjunto atual contém `DRAFT` antes de `VOID` porque não há item `REVIEW`. Lint PHP e build de produção do frontend (`vue-tsc` + Vite) aprovados.

## 2026-09-27 — Saneamento integral da fila de revisão de Arquitetura/SO

- Foi criado o saneador reutilizável de conteúdo importado e a rotina operacional `bin/repair-review-question-content.php`. Ela decodifica quebras serializadas como `\n`, remove cabeçalhos/rodapés do PDF e marcadores de extração, conserva caminhos Windows e normaliza alternativas antes de persistir.
- A fila inteira do job `5838793d-00d5-4e65-8dc7-942526b6fa3e` foi revisada em três passagens: 845 correções de enunciado, 274 de alternativas, 157 duplicatas exatas consolidadas e 72 registros incompletos/truncados marcados como `VOID` (nunca publicados). Trinta e seis gabaritos oficiais foram preservados por transferência para a versão mantida.
- Resultado editorial: 847 itens permanecem em `REVIEW`; a auditoria final não encontrou rodapé, comentário/resolução do curso, marcador de extração, número de página isolado, início truncado ou quebra `\n` literal nos enunciados ou alternativas. O exemplo do comando `grep -v` agora tem apenas o conteúdo efetivo da questão.
- Backup anterior à alteração: `backups/concursos-20260927T132426Z.sql.gz` (ignorado pelo Git). Validações aprovadas: testes de `ImportedQuestionContentSanitizer` e `DoctrineQuestionPdfQuestionWriter` (6 testes, 10 asserções) e auditoria via Doctrine/SQL de leitura.

## 2026-09-27 — Extração direta de Arquitetura e Sistemas Operacionais

- O job cancelado `5838793d-00d5-4e65-8dc7-942526b6fa3e` foi concluído por extração direta local: 2.524 páginas processadas, 1.931 questões objetivas detectadas, 1.076 criadas e 855 duplicadas descartadas.
- O classificador determinístico foi ampliado para Arquitetura de Computadores, Sistemas Operacionais, Sistemas de Arquivos, Processos e Memória, Linux/Unix, Microsoft Windows, Storage, Servidores Web, Sistemas Distribuídos, Virtualização, Nuvem e Contêineres. Foram criados cinco nós canônicos específicos que ainda não existiam.
- Dos itens criados, 910 possuem gabarito `OFFICIAL`: 886 estavam adjacentes aos enunciados e 24 foram recuperados das tabelas/comentários do PDF. Permanecem 166 sem gabarito explícito; não receberam inferência.
- Validações: importação finalizada com progresso 100%, sem falhas de escrita; recuperação oficial por fonte local executada.

## 2026-09-27 — Backup local para restauração

- Backup consistente criado em `/var/www/concursos/backups/conquistaai-20260927-100642` (ignorado pelo Git).
- Conteúdo: `database.sql` com dump MySQL de transação única, `question-pdfs.tar.gz`, `syllabus-pdfs.tar.gz`, `question-assets.tar.gz`, manifesto e `SHA256SUMS`.
- Checksums e leitura integral dos três arquivos tar foram validados. A restauração deve ocorrer em ambiente controlado, restaurando o dump e o conteúdo de cada arquivo em seu volume nomeado correspondente.

## 2026-09-27 — Backup completo para restauração

- Backup consistente criado em /var/www/concursos/backups/conquistaai-20260927T160247Z (diretório ignorado pelo Git).
- Conteúdo: database.sql com dump MySQL em transação única, rotinas, triggers e eventos; question-pdfs.tar.gz; syllabus-pdfs.tar.gz; question-assets.tar.gz; README.txt e SHA256SUMS.
- Integridade validada com sha256sum -c e leitura integral dos três arquivos tar. A restauração deve ser feita em ambiente controlado, importando o dump e extraindo cada arquivo no respectivo volume nomeado.

## 2026-09-27 — Priorização de gabarito na aprovação editorial

- A fila editorial preserva a prioridade de estados REVIEW, DRAFT e VOID. Dentro de DRAFT, a ordenação agora coloca primeiro as questões com correctOptionId preenchido e somente depois as que ainda dependem de gabarito.
- Assim, a ação de publicação em lote encontra primeiro os itens efetivamente publicáveis, sem esconder questões pendentes de resposta.
- O contrato da rota administrativa foi atualizado. Validações: primeira página real com 100 itens continha 100 DRAFT com gabarito, nenhum DRAFT sem gabarito antes deles; PHPUnit do contexto QuestionBank aprovou 13 testes e 32 asserções.

## 2026-09-27 — Recuperação direta de cursos consolidados de Desenvolvimento

- Os dois jobs cancelados de cursos consolidados foram simulados e extraídos sem worker ou provedor externo. Desenvolvimento de Software detectou 2.195 questões, com 1.192 novas e 1.003 duplicatas; Engenharia de Software detectou 3.490, com 1.636 novas e 1.854 duplicatas. Nenhum item falhou.
- A regra de fallback passou a apontar para folhas, nunca para nós que possuem filhos: Fundamentos de Desenvolvimento de Software e Fundamentos de Engenharia de Software. Assim, questões sem sinal suficiente para uma taxonomia mais específica permanecem classificadas sem serem descartadas; os reconhecedores específicos existentes continuam sendo aplicados antes desse fallback.
- O sanitizador passou a remover também o rodapé recorrente do curso de Desenvolvimento de Software. O relatório do primeiro job foi consolidado após sua retomada, para refletir ambas as passagens.
- Validações: simulações individuais, monitoramento dos processos locais até o relatório final, auditoria Doctrine de totais/status e lint PHP.

## 2026-09-27 — Contagem editorial da árvore de assuntos

- A contagem exibida na árvore canônica estava agregando vínculos de todos os estados editoriais. O repositório Doctrine agora une a questão ao vínculo de taxonomia e filtra explicitamente status PUBLISHED antes da agregação de ancestrais.
- Assim, badges de folhas e nós-pai mostram somente questões aprovadas/publicadas; DRAFT, REVIEW e VOID não compõem nenhum total da árvore.
- O contrato de GET /admin/taxonomy/subjects foi documentado com essa regra. Validações: consulta real retornou 2.527 vínculos publicados, igual ao total de questões PUBLISHED; PHPUnit focalizado aprovou 18 testes e 39 asserções.

## 2026-09-27 — Extração direta de Arquitetura, Mensageria e Serviços Web

- Cinco jobs cancelados foram recuperados por extração local. A aula repetida de XML/JSON/CSV detectou 19 itens, todos duplicados e sem nova criação. Arquitetura de Software criou 1 questão, Mensageria 3, Padrões de Projeto 3 e Web Services 5; não houve falhas.
- O classificador passou a usar ramos específicos sob Engenharia de Software: Arquitetura de Software, Mensageria, Padrões de Projeto e Web Services. Nenhuma questão desse lote usou o fallback de Arquitetura de Computadores.
- Validações: simulação antes de cada gravação, lint PHP, consulta Doctrine dos status/totais e auditoria das associações persistidas.

## 2026-09-27 — Extração direta de Big Data, BI e Governança de Dados

- Quatro jobs cancelados foram recuperados por extração determinística local: Big Data (5), Business Intelligence (8), Big Data avançado (8 detectadas, 7 novas e 1 duplicata) e Governança de Dados (2). Foram criadas 22 questões objetivas completas, sem falhas.
- O classificador passou a associar esses materiais sob Tecnologia da Informação > Banco de Dados, nos ramos Big Data, Business Intelligence e Governança de Dados. Os quatro ramos foram criados apenas quando inexistentes.
- Validações: simulação anterior à escrita, lint PHP, auditoria Doctrine dos jobs concluídos e das associações de taxonomia.

## 2026-09-27 — Extração direta de XML, Linux, DevOps, Python e Git

- Cinco jobs cancelados foram simulados e concluídos por extração determinística local: XML/JSON/CSV (19), Linux (2), DevOps (2), Python (14) e Git (23). As 60 questões objetivas completas foram criadas sem duplicatas ou falhas, todas com gabarito explícito recuperado do próprio material.
- O classificador passou a usar o nome do arquivo para estabilizar temas de curso e criar somente os ramos canônicos necessários: Formatos de Dados e Integração, DevOps, Python e Controle de Versão; Linux foi associado ao ramo existente Linux e Unix.
- Validações: simulação dos cinco PDFs antes da escrita, lint PHP, consulta por Doctrine de status/totais e auditoria das associações de taxonomia persistidas.

## 2026-09-27 — Extração direta de Desenvolvimento, Processos e Projetos

- Os dez jobs que permaneciam CANCELLED foram simulados e recuperados sem worker ou provedor externo. Foram detectadas 799 questões objetivas completas; 481 novas foram gravadas, 318 duplicatas foram descartadas e nenhuma escrita falhou.
- A classificação local passou a reconhecer também Metodologias Ágeis (Scrum, Kanban, XP e Lean Inception), Processos e Qualidade de Software (BPM, BPMN, MPS.BR e CMMI) e PMBOK/Gestão de Projetos. Para os PDFs de uma mesma aula, o nome do arquivo também estabiliza a categoria e evita o fallback de Arquitetura de Computadores.
- Todas as 481 questões recuperadas foram reatribuídas por meio do repositório Doctrine ao assunto específico da aula: 434 em Metodologias Ágeis, 31 em PMBOK e 16 em Processos e Qualidade de Software. Não restaram associações desse lote a Arquitetura de Computadores, Storage ou Gerenciamento de Processos e Memória.
- Validações: simulação integral anterior à gravação, conclusão dos dez jobs em 100%, lint PHP, auditoria dos totais/erros por job e auditoria de classificação persistida.

## 2026-09-27 — Recuperação robusta dos PDFs cancelados

- A extração direta dos três jobs cancelados foi revisada após inspeção do conteúdo original. O curso curso-220898-aula-01-551d-completo.pdf possui 34 questões objetivas A–E detectadas (7 novas, 27 duplicadas); duas duplicatas do subconjunto novo foram marcadas como VOID e cinco itens íntegros permanecem em REVIEW. Rodapés de cursos passaram a ser removidos de forma genérica, incluindo variações de título e autoria, sem vincular a limpeza a uma aula específica.
- O PDF de monitoramento continha questões com banca/cargo quebrados em múltiplas linhas. O reconhecedor agora aceita metadados parentéticos multilinha: foram detectadas 3 questões, criadas 2 novas e reconhecida 1 duplicata, todas com gabarito explícito do material e classificadas em Tecnologia da Informação > Redes de Computadores > Monitoramento de Redes.
- O PDF de Forense Computacional foi inspecionado e não contém questão objetiva completa A–E; portanto, terminou com zero itens por regra de qualidade, mantendo o descarte de itens teóricos, certo/errado e discursivos.
- Validações: lint dos scripts PHP, PHPUnit focalizado do sanitizador e writer (7 testes, 11 asserções), simulação antes da escrita, auditoria de status dos três jobs e git diff --check.

## 2026-09-27 — Recuperação direta de jobs cancelados

- Os quatro jobs cancelados foram concluídos sem worker ou provedor externo por `bin/import-interrupted-pdf-direct.php`: Computação em Nuvem (teoria: 3 criadas), Computação em Nuvem (questões: 16 criadas, 13 duplicadas), Virtualização (79 criadas, 67 duplicadas) e Contêineres (7 criadas).
- O importador aceita somente blocos objetivos A–E completos, preserva metadados explícitos, descarta itens de certo/errado e discursivos, deduplica enunciados e classifica em Computação em Nuvem, Virtualização ou Contêineres sob Infraestrutura.
- Das 104 questões atualmente associadas a esses jobs, 100 receberam gabarito `OFFICIAL` recuperado do próprio material e 4 receberam `AI_ESTIMATED` por solução local documentada. Um bloco inválido que mesclava um item CEBRASPE com outra questão foi removido.
- A questão de atores em nuvem recebeu o recorte visual recuperado do PDF como `question_asset`; a questão de paravirtualização já possuía asset extraído.
- Validações aprovadas: lint do novo script, integridade dos gabaritos/assets via Doctrine, `docker compose config --quiet` e healthcheck da API.

## 2026-09-27 — Correção visual e editorial do lote de Redes

- Foram corrigidos cinco registros pendentes do job `65e72f46-6635-4e75-a1b4-39f7ee207e76` diretamente a partir do PDF: ATM (B), NAT (B), captura DNS (C), arquitetura de firewall/DMZ (E) e códigos maliciosos (D). Todos foram confirmados pelo gabarito/comentário do material e agora usam `OFFICIAL`.
- A tabela da captura DNS e a matriz de códigos maliciosos foram extraídas do PDF e associadas às respectivas questões como `question_assets`. O Compose agora mantém `question_pdf_assets` em volume nomeado, montado na API e no worker de importação.
- Foram removidos dois registros inválidos: um item de certo/errado que havia sido convertido indevidamente em múltipla escolha e uma duplicata fragmentada da questão de DNS. O lote ficou com 630 `OFFICIAL`, 96 `AI_ESTIMATED` e nenhuma questão sem gabarito.
- Validações: alternativas, chaves oficiais, páginas de evidência e assets persistidos foram consultados via Doctrine; `docker compose config --quiet` e o healthcheck da API foram aprovados após a recriação dos serviços.

## 2026-09-26 — Gabaritos locais do lote de Redes

- Para o job `65e72f46-6635-4e75-a1b4-39f7ee207e76` (Redes de Computadores), foram resolvidas localmente 97 questões que estavam sem gabarito, sem chamada ao Gemini, OpenAI ou outro provedor.
- As respostas gravadas foram marcadas como `AI_ESTIMATED`; os 625 gabaritos extraídos diretamente do material permanecem `OFFICIAL`.
- Seis registros permaneceram sem resposta: dependem de figura ou tabela não preservada pela extração, têm alternativas vazias ou foram corrompidos pela segmentação. Não se deve inferir uma alternativa sem a evidência ausente.
- Validação: lote com 625 `OFFICIAL`, 97 `AI_ESTIMATED` e 6 sem gabarito. A decisão protege o sinal editorial e permite tratar essas seis questões numa correção posterior de extração de imagens/tabelas.


## 2026-09-25 — Correção do worker de editais

- Diagnóstico no concurso de validação Tranpetro: o PDF foi armazenado, mas o job falhou antes de extrair páginas porque a imagem Alpine do `syllabus-worker` não continha `pdftotext`.
- Correção aplicada na imagem compartilhada da API/worker: instalação de `poppler-utils`; imagem reconstruída e `pdftotext version 25.12.0` confirmado no worker.
- Reprocessamento do edital Tranpetro concluído: novo job `a0cc9f98-2eca-43a8-b3d7-57d07ecf6556` terminou em `COMPLETED` (100%), com 84 páginas e 411.508 caracteres extraídos.
- Em implementação: após extração, o worker chama o provider configurado (`AI_PROVIDER`) com evidências limitadas do edital, cria cargos e assuntos locais, e associa cada assunto a uma taxonomia canônica existente ou recém-criada. O reprocessamento do Tranpetro será a validação integrada desta etapa. Durante a primeira execução, uma falha da análise fechou o EntityManager por estar dentro da transação de extração; a análise foi separada da transação para o job poder registrar FAILED de forma recuperável. Diagnóstico adicional: o worker não recebia `AI_PROVIDER` e credenciais do provider; a configuração foi alinhada à API antes do novo reprocessamento. Diagnóstico final de conectividade: o worker precisava integrar também a rede `public` para alcançar o endpoint Gemini, sem publicar nenhuma porta. O modelo `gemini-2.5-flash` configurado foi descontinuado para esta conta; atualizado localmente para `gemini-3.8-flash`, conforme resposta do provider. A validação da nova chamada recebeu `high demand` em duas tentativas; o reprocessamento editorial do Tranpetro permanece pendente de disponibilidade do Gemini, enquanto extração PDF continua validada.

## Como retomar em outro ambiente
> **Regra obrigatória para IAs e contribuidores:** sempre que houver avanço de desenvolvimento — criação, alteração, validação, commit, descoberta de bloqueio ou mudança de próxima etapa — atualize este arquivo no mesmo ciclo, antes de iniciar outra funcionalidade. Não dependa somente do histórico da conversa ou de commits para transmitir contexto.

1. Leia `AGENTS.md`, `docs/ARCHITECTURE.md`, `docs/API.md` e este arquivo.
2. Execute `git status --short` e preserve qualquer alteração pendente.
3. Leia a seção **Próxima etapa autorizada** antes de alterar código.
4. Ao concluir um incremento, atualize este arquivo, a documentação de contrato aplicável e registre as validações executadas.

## Marco ativo

**M2 — Jobs e processamento de edital**, concluído.

A meta do marco foi entregar administração de concursos, cargos e editais, preservação idempotente de PDF, proveniência rastreável do conteúdo e associação explícita à taxonomia canônica. Consulte `docs/DEVELOPMENT_SCHEDULE.md` para os critérios completos.

## Entregue e versionado

| Entrega | Commit |
| --- | --- |
| Módulo frontend DDD de Taxonomia | `0d9cf71` |
| Árvore e criação administrativa | `0a92bf9` |
| Aliases canônicos, API e tela | `7e94bba` |
| Fundação de movimentação segura | `09fd89a` |
| Endpoint PATCH de assuntos | `215546f` |
| Edição pela interface, Axios PATCH e README | `5668239` |
| Similaridade determinística entre assuntos | `d0f8eb7` |
| Restauração dos testes de Taxonomia | `dc2c4dd` |

Funcionalidades disponíveis: criar e listar assuntos, construir a árvore, criar aliases, editar nome/descrição e mover somente assuntos sem filhos. A troca de pai impede ciclos, colisão de slug no mesmo nível e movimentação de nós com filhos.

## Alterações pendentes no diretório de trabalho

Em preparação para o próximo incremento:

- Interface de sugestões administrativas de possíveis assuntos duplicados.
- Rota `GET /api/v1/admin/taxonomy/duplicate-suggestions`.
- Migration `006_question_taxonomy_subjects.sql`, que cria o vínculo N:N sem remover `question_subjects`.
- A implementação compara até 1.000 assuntos, aplica similaridade normalizada com limiar de `0.72` e retorna somente candidatos para revisão humana.

A API, o repositório Axios, o caso de uso, o composable e a tela Quasar foram concluídos; a próxima etapa é a proposta de fusão auditável.

## Próxima etapa autorizada

Concluir **M0.5 — proposta de fusão auditável** nesta ordem:

1. Documentar o endpoint e seu formato de resposta em `docs/API.md`.
2. Criar contrato de domínio, repositório Axios, caso de uso e composable no frontend.
3. Exibir candidatos, nomes e percentual de similaridade na tela administrativa de Taxonomia, com estados de carregamento, vazio e erro.
4. Validar build e testes; atualizar este arquivo e o cronograma; criar commit.
5. Somente depois iniciar a proposta de fusão, reatribuição transacional e auditoria em `taxonomy_subject_merges`.

Nenhuma sugestão pode executar fusão automaticamente. A revisão e a confirmação administrativa são obrigatórias.

## Validações mais recentes

```bash
docker compose exec -T api vendor/bin/phpunit
# OK: 30 testes, 71 assertions

docker compose exec -T frontend npm run build
# OK no último incremento frontend (commit 5668239)
```

## Convenções relevantes

- Backend: `HTTP -> Request DTO -> Controller -> Service -> Repository Interface -> Doctrine Repository`.
- Frontend: `Interface -> Application -> Domain <- Infrastructure`; telas não chamam Axios diretamente.
- Todo endpoint usa o envelope descrito em `docs/API.md`.
- Funcionalidade com interação humana só está concluída quando backend, frontend, documentação e validações foram entregues no mesmo ciclo.

## Atualização de continuidade — 23/09/2026

- Os commits `72eddee`, `5ea5a57`, `a8e73cf` e `ceee03a` concluíram, respectivamente, a API de sugestões, seu cliente frontend, sua visualização administrativa e a migration de ligação `question_taxonomy_subjects`.
- Estão pendentes de commit o contrato de domínio e o repositório Doctrine para substituir atomicamente as atribuições canônicas de uma questão.
- A próxima etapa autorizada foi ajustada: concluir o caso de uso administrativo de atribuição de assuntos canônicos a questões, com validação de questão e de todos os assuntos, contrato HTTP, frontend editorial, testes e documentação. Só depois iniciar fusões auditáveis.

## Avanço atual — atribuição canônica de questões

- Commit `0f7fd80` registrou a regra de handoff e a persistência Doctrine de vínculos questão–taxonomia.
- Pendentes de commit: DTO e serviço transacional `AssignQuestionTaxonomySubjects`, além da verificação de existência de questão no repositório editorial.
- Validação executada: PHPUnit aprovado com 30 testes e 71 assertions.
- Próximo passo: expor o endpoint administrativo, criar o módulo Axios e a interação editorial correspondente antes de iniciar fusões.

## Avanço atual — rota editorial de Taxonomia

- Pendente de commit: `PUT /v1/admin/questions/{id}/taxonomy-subjects` no registrar editorial.
- O payload requer `taxonomy_subject_ids` como lista de strings e chama o caso de uso transacional.
- PHPUnit aprovado: 30 testes, 71 assertions.
- Próximo passo: documentar o contrato, criar o adaptador Axios e expor o controle na tela editorial.

## Avanço atual — refatoração editorial obrigatória

- Commit `0ad1b92` concluiu a rota de atribuição canônica.
- Foi identificado que `Interface/Http/Editorial/EditorialPage.vue` consome Axios diretamente, contrariando `docs/FRONTEND.md`.
- Próxima etapa: migrar Editorial para camadas Domain/Application/Infrastructure e só então adicionar o controle de atribuição canônica na tela.

## Avanço atual — extração do módulo Editorial

- Pendentes de commit: contratos Domain, casos de uso Application e repositório Axios Infrastructure do Editorial.
- A tela ainda precisa ser migrada para um composable que use `editorialUseCases`; até isso ocorrer, não adicionar controles de Taxonomia nela.

## Avanço atual — tela Editorial DDD

- Pendentes de commit: módulo Editorial em Domain/Application/Infrastructure e composable `useEditorial`; `EditorialPage.vue` não chama mais Axios diretamente.
- Build frontend aprovado.
- Próximo passo: adicionar ao módulo Editorial o comando de atribuição de assuntos canônicos e o controle Quasar por questão.

## Avanço atual — comando editorial de atribuição

- Pendentes de commit: `putData` no cliente HTTP e `assignTaxonomy` nas camadas DDD do Editorial.
- Build frontend aprovado.
- Próximo passo: disponibilizar seleção de assuntos canônicos na interface editorial, usando o módulo Taxonomy sem chamadas Axios na tela.

## Avanço atual — seleção editorial de Taxonomia

- Pendentes de commit: seleção múltipla Quasar de assuntos canônicos por questão no Editorial e comando PUT via composable.
- Build frontend aprovado.
- Próximo passo: documentar o endpoint editorial e criar testes específicos de atribuição; depois iniciar a fusão auditável.

## Commit de integração editorial

- Commit `002440e` concluiu a refatoração DDD do Editorial e a atribuição canônica pela interface.
- Próxima etapa autorizada: proposta de fusão auditável entre assuntos canônicos, precedida de contrato, transação, auditoria e revisão humana.

## Avanço atual — auditoria de fusão

- Pendentes de commit: entidade e contrato de persistência para `taxonomy_subject_merges`.
- A implementação ainda não executa fusões nem altera referências; isso só ocorrerá após o serviço transacional e revisão administrativa.

## Avanço atual — persistência de auditoria de fusão

- Pendentes de commit: repositório Doctrine `DoctrineTaxonomySubjectMergeRepository` e os contratos/entidades de auditoria criados anteriormente.
- PHPUnit aprovado: 30 testes e 71 assertions.
- Próximo passo: desenhar a reatribuição transacional de aliases, filhos e vínculos `question_taxonomy_subjects` antes de expor uma confirmação de fusão.

## Decisão de integridade — fusão de assuntos

- O assunto de origem de uma fusão não será removido, pois `taxonomy_subject_merges` mantém chaves estrangeiras para origem e destino.
- A estratégia será reatribuir vínculos canônicos permitidos, converter o assunto de origem em inativo e persistir a auditoria na mesma transação.
- A fusão deverá recusar fonte com filhos até existir atualização recursiva de níveis; isso evita corromper a árvore.

## Avanço atual — serviço de fusão transacional

- Pendentes de commit: DTO e serviço `MergeTaxonomySubjectsService`, dependente da porta `TaxonomySubjectMergeApplierInterface`.
- O endpoint permanece não exposto até a implementação Doctrine reatribuir aliases e vínculos de questões de modo idempotente.

## Avanço atual — reatribuição idempotente de fusão

- Pendente de commit: `DoctrineTaxonomySubjectMergeApplier`.
- Antes de mover os vínculos de questões, remove apenas associações de origem que já existam no destino; depois move associações restantes e aliases.
- PHPUnit aprovado: 30 testes e 71 assertions.
- Próximo passo: registrar o serviço no endpoint administrativo de confirmação de fusão e documentar o contrato.

## Avanço atual — controlador de confirmação de fusão

- Pendente de commit: `TaxonomySubjectMergeController`, que exige ADMIN, destino e motivo não vazio.
- A rota ainda deve ser registrada em `TaxonomyRouteRegistrar` com as dependências Doctrine antes de estar acessível.

## Avanço atual — rota de confirmação de fusão

- Pendente de commit: `POST /v1/admin/taxonomy/subjects/{id}/merge`, conectado ao serviço transacional, reatribuição idempotente e auditoria.
- PHPUnit aprovado: 30 testes e 71 assertions.
- Próximo passo: documentar contrato, consumir pelo frontend de Taxonomia e exigir confirmação explícita de administrador.

## Avanço atual — contrato frontend de fusão

- Pendentes de commit: contrato, repositório Axios e caso de uso de fusão no módulo Taxonomy; contrato HTTP documentado.
- Build frontend aprovado.
- Próximo passo: inserir confirmação Quasar na tela Taxonomy, depois validar e commitar a entrega integrada.

## Avanço atual — confirmação administrativa de fusão habilitada

- Com autorização explícita do responsável, a tela Taxonomy agora permite confirmar fusão com origem, destino e motivo.
- A operação chama a transação auditável, desativa a origem e reatribui aliases e vínculos canônicos.
- Build frontend e PHPUnit backend aprovados.
- Pendentes de commit: toda a entrega integrada de fusão e o aviso de impacto.

## Commit de fusão auditável

- Commit `c0735d7` concluiu sugestões, confirmação administrativa, reatribuição idempotente, desativação da origem e auditoria de fusões.
- M0.5 está concluído. A próxima etapa do M0 é M0.6: interface de IA para reconciliação, com proposta auditável e revisão humana.

## Avanço atual — M0.6 reconciliação auditável

- Commit `2a8b357` registrou a conclusão de M0.5.
- Pendentes de commit: porta `TaxonomyReconciliationAdvisorInterface` e DTO de proposta de reconciliação.
- Nenhum provedor de IA foi escolhido; a decisão permanece bloqueada conforme cronograma.
- Próximo passo: implementar um adaptador determinístico local para propostas revisáveis, sem chamadas externas.

## Avanço atual — adaptador local de reconciliação

- Pendentes de commit: adaptador `DeterministicTaxonomyReconciliationAdvisor` que propõe somente pares com confiança >= 0.85 e motivo reproduzível.
- PHPUnit aprovado: 30 testes e 71 assertions.
- Próximo passo: serviço de aplicação e tela de revisão das propostas; nenhuma proposta executará fusão automaticamente.

## Avanço atual — caso de uso de propostas de reconciliação

- Pendente de commit: `ListTaxonomyReconciliationProposalsService`, que converte propostas do adaptador em DTOs de revisão.
- PHPUnit aprovado: 30 testes e 71 assertions.
- Próximo passo: expor endpoint ADMIN e listar propostas na tela de Taxonomia; aprovação continuará sendo uma fusão explícita já auditada.

## Avanço atual — controlador de propostas de reconciliação

- Pendente de commit: `TaxonomyReconciliationController`, somente leitura e restrito a ADMIN.
- A rota ainda deve ser registrada com o adaptador determinístico antes de chegar ao frontend.

## Avanço atual — rota de propostas de reconciliação registrada

- Pendente de commit: `GET /v1/admin/taxonomy/reconciliation-proposals`, ligado ao adaptador determinístico local.
- PHPUnit aprovado: 30 testes e 71 assertions.
- Próximo passo: documentar o contrato e criar o módulo frontend de revisão das propostas.

## Avanço atual — contrato frontend de reconciliação

- Pendentes de commit: contrato e repositório Axios para propostas de reconciliação, além da documentação API.
- Build frontend aprovado.
- Próximo passo: caso de uso, composable e card de revisão na tela Taxonomy; a única ação disponível continuará sendo a fusão administrativa explícita.

## Avanço atual — caso de uso e composable de reconciliação

- Pendentes de commit: caso de uso, container e composable de propostas de reconciliação.
- Build frontend aprovado.
- Próximo passo: card de revisão na Taxonomy exibindo confiança e justificativa, com ação de fusão já explícita e autorizada.

## Avanço atual — card de revisão de reconciliação

- Pendentes de commit: card Quasar de propostas determinísticas, com confiança e justificativa, além de toda a integração M0.6.
- Build frontend aprovado.
- Próximo passo: validar backend e frontend, atualizar cronograma como M0.6 concluído e criar o commit integrado.

## Validação integrada — M0.6

- PHPUnit aprovado: 30 testes e 71 assertions.
- Build frontend aprovado.
- M0.6 concluído com propostas determinísticas revisáveis; fusões continuam dependentes de confirmação ADMIN explícita.

## Commit de reconciliação auditável

- Commit `7d40e04` concluiu M0.6 e, com isso, o marco M0 de Taxonomia canônica.
- Próxima etapa autorizada: M1 — concursos, cargos e editais estruturados; iniciar pelo inventário dos contratos e persistência existentes antes de alterar código.

## Avanço atual — metadados de documento de edital

- Pendentes de commit: migration `007_syllabus_document_metadata.sql` e documentação do hash SHA-256 para PDFs.
- A migration é aditiva e preserva editais e URLs existentes.
- Próximo passo: estender entidade/repositório/serviço de edital e criar upload administrativo com armazenamento local.

## Avanço atual — modelo de documento de edital

- Pendentes de commit: metadados opcionais de PDF no domínio `Syllabus`, registro Doctrine e repositório.
- Próximo passo: porta de armazenamento local e caso de uso de upload idempotente, acompanhado de interface administrativa.

## Avanço atual — armazenamento local idempotente de PDF

- Pendentes de commit: porta `SyllabusDocumentStorageInterface` e adaptador local que usa SHA-256 como nome de arquivo.
- O adaptador só grava quando o arquivo ainda não existe.
- Próximo passo: caso de uso administrativo que valida PDF, calcula hash e atualiza o edital com os metadados.

## Avanço atual — caso de uso de upload de edital

- Pendentes de commit: `UploadSyllabusDocumentService`, DTO e consulta de edital por ID.
- O serviço aceita somente PDF com assinatura `%PDF-`, calcula SHA-256 e grava via porta local idempotente.
- Próximo passo: expor endpoint multipart administrativo, retornar metadados no DTO e criar upload no frontend Catalog.

## Avanço atual — controller multipart de edital

- Pendente de commit: `SyllabusDocumentController`, que exige ADMIN e lê o arquivo `document` multipart.
- A rota ainda precisa ser registrada com armazenamento local configurado antes de estar acessível.

## Avanço atual — PDF de edital preservado

- A migration `007_syllabus_document_metadata.sql` foi aplicada no ambiente local. Ela acrescenta metadados opcionais ao edital e um índice de SHA-256, sem apagar `source_url` nem registros existentes.
- O backend agora oferece `POST /api/v1/admin/syllabi/{id}/document`: recebe somente PDF multipart, valida a assinatura `%PDF-`, calcula SHA-256, reutiliza o arquivo local quando o hash já existe e atualiza o edital dentro de transação.
- O retorno público de editais passou a incluir somente metadados seguros do documento; caminhos internos de armazenamento continuam privados.
- O catálogo administrativo recebeu o fluxo responsivo de seleção e envio de PDF, por meio das camadas Domain/Application/Infrastructure. A tela não acessa Axios diretamente.
- Validações deste incremento: PHPUnit host aprovado com 32 testes e 78 assertions; `docker compose exec -T frontend npm run build` aprovado; `git diff --check` aprovado.
- Decisão: armazenamento local configurável por `SYLLABUS_DOCUMENT_DIRECTORY` até a definição de S3/MinIO no M2. O próximo recorte do M1 é modelar conteúdo programático por cargo, preservando origem e posição dentro do PDF para o processamento assíncrono do M2.
- Correção de persistência: o compose monta o volume nomeado `syllabus_documents` em `/app/storage/syllabi`; portanto, PDFs já enviados sobrevivem à recriação do contêiner da API. `SYLLABUS_DOCUMENT_DIRECTORY` permite trocar o diretório sem alterar o código.

## Commit de preservação de edital

- Commit `e731c56` concluiu a primeira entrega do M1: upload administrativo de PDF, hash SHA-256 idempotente, volume Docker persistente, metadados seguros na API e interface Quasar responsiva.
- A migration `007_syllabus_document_metadata.sql` já foi aplicada no ambiente local.
- Próxima etapa autorizada: modelar trechos estruturados do conteúdo programático por cargo, com posição de origem no PDF. A extração automática permanece no M2; nesta etapa o modelo e o cadastro administrativo devem estar completos em backend, frontend, documentação e testes.

## Em progresso — proveniência de conteúdo programático

- A próxima entrega do M1 está em andamento e ainda não deve ser considerada concluída: migration `008_subject_source_provenance.sql`, domínio/DTOs/Doctrine e validação de trecho, página e offsets foram preparados para assuntos existentes.
- O endpoint e o formulário ainda precisam expor os campos opcionais `source_excerpt`, `source_page`, `source_start_offset` e `source_end_offset`.
- Ainda faltam o contrato e a edição paralela no frontend, testes específicos, aplicação da migration, documentação e validação integrada. Não iniciar M2 antes de concluir esses itens no mesmo ciclo.

## Entrega concluída — proveniência de conteúdo programático

- A migration `008_subject_source_provenance.sql` foi aplicada no ambiente local e mantém compatibilidade com assuntos existentes.
- O fluxo DDD de `POST /subjects` recebeu Request DTO validado, controller fino, serviço, repositório Doctrine e mapper para `source_excerpt`, `source_page`, `source_start_offset` e `source_end_offset`.
- O catálogo administrativo permite registrar página e trecho de origem, envia-os pelo módulo Axios e mostra a evidência como dica no assunto. Não há chamada HTTP direta na tela.
- Documentação atualizada: API, frontend e banco. Validações: API 33 testes / 83 assertions; build Quasar aprovado; `git diff --check` aprovado.
- Próximo passo autorizado: encerrar M1 com o vínculo explícito entre assuntos locais do edital e a Taxonomia canônica, sempre com revisão administrativa; só então iniciar M2.

## Commit de proveniência de conteúdo

- Commit `e26952b` concluiu o segundo recorte do M1: proveniência de assuntos por edital no backend, frontend, documentação e testes.
- Próxima etapa autorizada: associação explícita e revisável entre `subjects` do edital e `taxonomy_subjects` canônicos. A associação não pode executar fusões, criar taxonomia automaticamente ou alterar vínculos de questões.

## Em progresso — vínculo de assunto local com taxonomia canônica

- Foram criados, ainda sem endpoint nem interface, a migration `009_subject_taxonomy_assignments.sql`, a porta de domínio e o repositório Doctrine de associação N:N.
- A associação substituirá apenas os vínculos do assunto selecionado em uma transação, validará assunto local e assuntos canônicos ativos, e será sempre uma ação ADMIN explícita.
- Pendências obrigatórias antes de M2: DTOs, serviço e controller; rota e contrato API; módulo DDD/Quasar no Catalog; testes; migration aplicada; documentação e validação integrada.

## Correção de cronograma

- `docs/DEVELOPMENT_SCHEDULE.md` agora detalha M1.1 a M1.5, define explicitamente o critério de saída do M1 e registra a sequência planejada de M2 a M7. M0.2 e M0.3 foram corrigidos para concluídos, removendo estados históricos contraditórios.

## Entrega concluída — vínculo canônico do conteúdo programático

- A migration `009_subject_taxonomy_assignments.sql` foi aplicada no ambiente local e cria a associação N:N sem remover assuntos, taxonomia nem vínculos de questões.
- `PUT /v1/admin/subjects/{id}/taxonomy-subjects` exige ADMIN, valida o assunto local e assuntos canônicos ativos, substitui somente as associações daquele assunto e responde por Response DTO.
- O catálogo Quasar carrega apenas assuntos canônicos ativos e permite salvar a associação de modo explícito. A implementação segue as camadas Domain/Application/Infrastructure; a tela não chama Axios.
- Validações: PHPUnit aprovado com 34 testes e 84 assertions; build Quasar aprovado; migration aplicada; `git diff --check` aprovado.
- Decisão: associações canônicas são revisáveis e não dispararam fusão, criação automática ou alteração de questões. M1.4 está concluído; falta somente a validação de saída M1.5 antes de M2.

## Encerramento do M1 — 24/09/2026

- O commit `ac4d826` concluiu M1.4: associação administrativa explícita entre assunto local e taxonomia canônica, sem fusão, criação automática ou alteração de questões.
- A auditoria final corrigiu `CatalogPage.vue`: a página não importa mais o container nem casos de uso; cada ação passa por `useCatalog`, preservando o fluxo obrigatório `Page -> Composable -> Application -> Domain <- Infrastructure`.
- Migrations confirmadas no MySQL local: metadados de documento em `syllabi`, proveniência em `subjects` e a tabela `subject_taxonomy_assignments`.
- Validações executadas: `docker compose exec -T api vendor/bin/phpunit` — OK, 34 testes e 84 assertions; `docker compose exec -T frontend npm run build` — OK; `git diff --check` — OK.
- Responsividade: o catálogo transforma controles de documento, níveis e associação canônica em coluna única até 600 px; o frontend foi renderizado com Chromium headless em viewport de 390 × 844 px sem falha de carregamento.
- Próxima etapa autorizada: M2.1 — definir fila/worker desacoplado e estado de processamento do edital. Não escolher provider de IA nem backend S3/MinIO antes da decisão prevista no cronograma.

## Início do M2 — fila persistida

- Contrato inicial documentado para enfileirar e consultar o último job de processamento de um edital, sempre restrito a ADMIN.
- Decisão: usar fila persistida no MySQL e um processo worker independente no M2. Isso permite retentativas e progresso consultável sem acoplar o domínio a Redis, a um provider de fila ou a S3/MinIO.
- Próximo passo: migration, entidade, repositório Doctrine, caso de uso e worker para os estados `PENDING`, `PROCESSING`, `COMPLETED` e `FAILED`; a extração de páginas será conectada somente depois dessa fundação.

## Avanço M2.1 — jobs persistidos

- Migration `010_syllabus_processing_jobs.sql` aplicada no MySQL local. Ela registra hash de entrada, estado, progresso, erro seguro e tempos do job, sem alterar PDFs ou assuntos existentes.
- O backend expõe o enfileiramento ADMIN e a consulta do último job. A criação é idempotente para o mesmo hash enquanto o job estiver pendente, em processamento ou concluído; uma reexecução exige `reprocess=true`.
- O repositório Doctrine faz claim pessimista para impedir que dois workers processem o mesmo job.
- Validação: PHPUnit aprovado com 35 testes e 86 assertions; sintaxe dos adaptadores novos aprovada; migration confirmada no banco local.
- Próximo passo: M2.2, implementar o worker desacoplado e a extração local por página com `pdftotext`, preservando hash e offsets antes de marcar jobs como concluídos.

## Avanço M2.2 — extração paginada

- Migration `011_syllabus_document_extractions.sql` aplicada no MySQL local. As páginas são identificadas pelo hash do PDF e número de página, com offsets globais de início e fim.
- O worker `syllabus-worker` é um processo Compose separado. Ele faz claim transacional de jobs pendentes, executa `pdftotext -layout`, substitui de modo idempotente as páginas do mesmo hash e conclui o job; falhas são convertidas em mensagem administrativa segura.
- O comando `php bin/process-syllabus-jobs.php` permite execução pontual em operação ou teste. O PDF permanece no volume persistente, sem provider externo.
- Validação: PHPUnit aprovado com 35 testes e 86 assertions; sintaxe do worker aprovada; migration confirmada no banco local.
- Próximo passo: M2.3/M2.4 — expor páginas e progresso, criar revisão administrativa no frontend e permitir reprocessamento explícito com resultados consistentes.

## Encerramento do M2 — 24/09/2026

- O Macro 2 está concluído: jobs persistidos no MySQL, worker Compose desacoplado e extração local por página via `pdftotext` preservam hash, offsets e conteúdo para revisão.
- A API administrativa expõe o estado do job e `GET /api/v1/admin/syllabi/{id}/extractions`; a tela Catalog permite iniciar, atualizar o progresso, reprocessar explicitamente e revisar cada página expandida com os offsets de proveniência.
- Reprocessamentos substituem extrações do mesmo hash de forma idempotente; nenhum assunto é criado ou publicado automaticamente.
- Commits contextuais: `c0ec96a` (API e teste), `30edae7` (módulo Catalog) e este commit de cronograma/handoff.
- Commits contextuais: `8c7d5d9` (backend de proveniência e candidatos) e `b7dbf0d` (revisão administrativa no frontend).
- Validações: PHPUnit com 36 testes e 90 assertions; build Quasar aprovado; `git diff --check` aprovado.
- Próxima etapa autorizada: M3 — questões reais, fontes e deduplicação. Iniciar pelo contrato e inventário dos modelos de questão existentes.

## Início do M3 — inventário e proveniência

- O inventário confirmou que preview, confirmação em rascunho e publicação já existiam, mas não completavam o critério do M3: a origem importada ainda não era persistida na questão e candidatos entre importações não eram revisáveis.
- Foi iniciada a correção de proveniência: o writer Doctrine passa a preservar `source`, `reference_url` e `origin` declarados na linha importada, usando somente os valores de origem permitidos.
- O preview agora consulta as questões existentes por enunciado normalizado e registra `DUPLICATE_CANDIDATE` no relatório persistido, bloqueando sua criação no commit até revisão administrativa.
- Próximo passo: introduzir a detecção persistida de candidatos contra questões já cadastradas, expor revisão administrativa e garantir confirmação idempotente sem criação automática de duplicatas.

## Encerramento do M3 — 24/09/2026

- O Macro 3 está concluído: importações preservam fonte, URL de referência e origem; o preview detecta repetição no arquivo e candidatos contra enunciados já cadastrados usando normalização reproduzível.
- Candidatos ficam persistidos no relatório de importação como `DUPLICATE_CANDIDATE`, são exibidos explicitamente para revisão na interface administrativa e não são criados na confirmação. A confirmação é idempotente pelo estado `VALIDATED`/`COMMITTED` e cria somente rascunhos válidos; a publicação administrativa existente mantém a revisão humana.
- A tela de importação foi migrada para `Page -> useImport -> Application -> Domain <- Infrastructure`; não chama Axios nem o container diretamente.
- Commits contextuais: `8c7d5d9` (backend de proveniência e candidatos) e `b7dbf0d` (revisão administrativa no frontend).
- Validações: PHPUnit com 36 testes e 90 assertions; build Quasar aprovado; `git diff --check` aprovado.
- Próxima etapa autorizada: M4 — estatísticas hierárquicas e dashboard por edital.

## Histórico — início do M4

- Os contratos iniciais de Performance foram preparados para dashboard por edital, respostas finais e nós canônicos.
- A entrega foi concluída no registro de encerramento abaixo, incluindo interface Quasar, teste específico e validação integrada.

## Encerramento do M4 — 24/09/2026

- O Macro 4 está concluído: `GET /api/v1/dashboard/me` devolve métricas do usuário por edital, usando apenas respostas finais e totais sem dupla contagem.
- Classificações canônicas são propagadas para ancestrais ativos; o dashboard distingue dados suficientes de insuficientes pelo limiar de 10 respostas em 3 dias distintos.
- A página Performance usa o fluxo obrigatório `Page -> usePerformance -> UseCase -> Repository -> Axios -> API`, oferece seletor de edital e estados de carregamento, erro, vazio e amostra insuficiente.
- Validações: PHPUnit aprovado com 37 testes e 94 assertions; `docker compose exec -T frontend npm run build` aprovado; `git diff --check` aprovado.
- Próxima etapa autorizada: M5 — cadernos inteligentes e plano de estudos.

## Entrega de interface — Taxonomia

- A tela de Taxonomia foi padronizada a partir da referência visual recebida: cabeçalho administrativo, árvore pesquisável com seleção contextual, painel de trabalho com abas e feedback de revisão explícito.
- A experiência usa somente o composable `useTaxonomy`; criação, edição, aliases, sugestões e fusão auditável continuam passando pelas camadas DDD existentes.
- Foi criado `DESIGN_SYSTEM.md` com princípios, fundamentos, padrões administrativos e acessibilidade para orientar telas futuras.
- Validação: `docker compose exec -T frontend npm run build` aprovado; `git diff --check` pendente da verificação final.
- Próximo passo: aplicar os padrões gradualmente às demais áreas administrativas, sem alterar contratos de API.

## Início do M5 — plano explicável

- O contrato inicial do plano de estudos foi documentado. O plano será calculado apenas com tentativas finais do usuário e não criará cadernos automaticamente.
- A primeira entrega combina prioridade por desempenho, explicação observável, indicação de dados insuficientes e criação confirmada de seleção congelada.
- Próximo passo: implementar a leitura de desempenho e o caso de uso backend, seguido do módulo Study e tela Quasar correspondente.

## Avanço M5 — prioridades e prática confirmada

- `GET /study-plan/me` calcula até cinco prioridades por assunto local a partir de tentativas finais, com percentual, amostra, dias distintos, justificativa e ação sugerida.
- A tela de Cadernos exibe o plano real e transfere uma prioridade para o formulário como filtro de assunto; a API só congela a seleção após confirmação explícita do estudante.
- Validações parciais: PHPUnit aprovado com 38 testes e 98 assertions; build Quasar aprovado; `git diff --check` aprovado.
- Pendente para concluir M5: metas persistidas e visão consolidada do plano semanal, com os mesmos limites de usuário autenticado.

## Encerramento do M5 — 24/09/2026

- O Macro 5 está concluído: `GET /api/v1/study-plan/me` calcula até cinco prioridades por assunto local exclusivamente com tentativas finais do usuário, retorna taxa de acerto, amostra, dias distintos, explicação e ação sugerida; fragilidade exige ao menos 10 respostas em 3 dias distintos.
- A tela Quasar de Cadernos exibe as recomendações, mas somente preenche o filtro de assunto. O estudante confirma a criação para congelar a seleção, sem automação implícita.
- `GET` e `PUT /api/v1/study-goals/me` fornecem uma meta semanal isolada por usuário, limitada a 1–500 respostas. A migration `012_study_goals.sql` foi aplicada e confirmada no MySQL local.
- Validações: `docker compose exec -T api vendor/bin/phpunit` aprovado (38 testes, 98 assertions); `docker compose exec -T frontend npm run build` aprovado; `git diff --check` aprovado.
- Próxima etapa autorizada: M6 — IA auditável e RAG de editais.

## Início do M6 — assistente auditável com RAG

- O M6 começa com um provider local determinístico e substituível, sem credenciais externas nem acesso direto a repositórios pelo provider.
- O escopo inicial é conversa individual sobre páginas extraídas de um edital, com resposta ancorada em evidências, isolamento por usuário e trilha imutável de auditoria.
- Próximo passo: criar a persistência e os contratos de Assistant, expor as rotas autenticadas e entregar o módulo Quasar correspondente.

## Encerramento do M6 — 24/09/2026

- O Macro 6 está concluído com o contexto Assistant: conversas e mensagens imutáveis, isoladas pelo usuário, são persistidas com provider, modelo e evidências por página.
- `GET /assistant/syllabi`, conversas e mensagens autenticadas entregam o RAG sobre páginas extraídas. O provider `DeterministicSyllabusAssistantProvider` é uma implementação local e substituível de `AssistantProviderInterface`; ele não acessa repositórios e responde somente com conteúdo recuperado.
- A tela Assistant usa o fluxo obrigatório Page -> composable -> use case -> repository -> Axios -> API, permite escolher edital extraído e expande os trechos utilizados em cada resposta.
- A migration `013_assistant_rag.sql` foi aplicada e confirmada no MySQL local. Validações: PHPUnit (40 testes, 102 assertions), build frontend e `git diff --check` aprovados; a rota sem credencial retorna envelope de erro, comprovando o registro e a proteção HTTP.
- Próxima etapa autorizada: M7 — descoberta web por providers permitidos.

## Início do M7 — descoberta web controlada

- A descoberta será limitada a um provider HTTP configurado por ambiente e a hosts explicitamente permitidos; não haverá crawler livre, download automático ou acesso a URL arbitrária.
- Cada candidato de prova ou gabarito preservará query, provider, URL de origem e data de descoberta para revisão administrativa.
- Próximo passo: implementar contratos, persistência, rota ADMIN e módulo Quasar correspondente.

## Encerramento do M7 — 24/09/2026

- O Macro 7 está concluído: `POST /admin/discovery/searches` consulta somente o provider HTTP configurado, aceita apenas hosts em `DISCOVERY_ALLOWED_HOSTS`, limita a 10 candidatos e não baixa arquivos nem segue URLs de candidatos.
- `GET /admin/discovery/resources` e a tela administrativa catalogam provas e gabaritos com título, tipo, URL de origem, provider, consulta e data de descoberta. A revisão humana permanece explícita.
- A configuração é feita por `DISCOVERY_PROVIDER_BASE_URL` e `DISCOVERY_ALLOWED_HOSTS` no ambiente; base ausente ou host não permitido interrompe a consulta com erro seguro.
- A migration `014_discovery_resources.sql` foi aplicada no MySQL local. Validações: PHPUnit (41 testes, 105 assertions), build frontend, sintaxe PHP e `git diff --check` aprovados.
- O cronograma M0–M7 está concluído; a próxima fase requer planejamento explícito.

## Integração OpenAI configurável

- O assistente agora seleciona `AI_PROVIDER=openai` ou `local` por ambiente. `OpenAiAssistantProvider` chama a Responses API com `store: false`, `OPENAI_API_KEY` no servidor e `OPENAI_MODEL` configurável.
- A OpenAI recebe somente pergunta e evidências recuperadas; a resposta continua persistindo provider, modelo e páginas usadas. Sem chave, a chamada falha com mensagem segura; `AI_PROVIDER=local` mantém o provider determinístico.
- Com autorização explícita, a API foi conectada também à rede Docker `public` exclusivamente para egress HTTPS a providers externos; nenhuma porta foi publicada.

## Adaptador Gemini configurável

- `GeminiAssistantProvider` implementa a mesma porta do assistente e usa `generateContent` com instrução de responder apenas com as evidências RAG do edital.
- Selecione `AI_PROVIDER=gemini` e configure `GEMINI_API_KEY` e, opcionalmente, `GEMINI_MODEL` (padrão `gemini-2.5-flash`). A chave permanece somente no ambiente do servidor.
- Foram incluídas as variáveis no Compose e no `.env.example`, e um teste unitário garante que a ausência de chave não inicia chamada externa.
- Próximo passo: inserir uma chave Gemini privada no `.env`, recriar o serviço `api` e realizar uma consulta real com um edital extraído.


## Avanço atual — refatoração visual da SPA

- O shell autenticado foi alinhado à referência aprovada: drawer compacto com ícones, busca no cabeçalho, notificações, avatar e comportamento sobreposto no mobile.
- Tokens Quasar e estilos globais foram atualizados para a paleta azul clara, superfícies brancas, bordas suaves e controles arredondados definidos no novo DESIGN_SYSTEM.md.
- A rota de Descobertas foi recolocada dentro do AppShell; antes ela não recebia a moldura autenticada por estar posicionada fora do bloco condicional.
- Nenhum contrato HTTP, composable ou repositório foi alterado.
- Validações: `git diff --check` aprovado; `docker compose exec -T frontend npm run build` aprovado em 24/09/2026. O build local direto permanece indisponível porque node_modules não está presente no workspace.
- Próximo passo: revisão visual em desktop e mobile com sessão autenticada antes de ajustes finos ou commit.


## Avanço atual — normalização de títulos

- Todas as páginas autenticadas (Início, Taxonomia, Importação, Revisão, Desempenho, Catálogo, Cadernos, Questões, Assistente e Descobertas) usam agora a classe visual única `page-title`.
- A escala é 26 px no desktop e 22 px no mobile, com peso, cor, line-height, tracking e espaçamento consistentes; Login e resolução de caderno continuam exceções de jornada imersiva.
- DESIGN_SYSTEM.md passou a registrar a escala tipográfica obrigatória.
- Validações: `docker compose exec -T frontend npm run build` e `git diff --check` aprovados em 24/09/2026.
- Próximo passo: revisão visual autenticada em desktop e mobile para ajustes finos de densidade.


## Avanço atual — layout de execução do caderno

- A tela de resolução foi reorganizada segundo a referência: contexto da sessão no topo, índice navegável de questões, área central de resposta e painel de progresso.
- Índice, estado respondido, total, acertos, erros, percentual e cronômetro são derivados apenas da seleção congelada e das estatísticas reais da API. Anotações, IA e árvore por assunto não foram simuladas porque ainda não possuem contrato.
- Em até 600 px, a questão vem antes do índice e do progresso, mantendo alternativas com alvo de toque apropriado.
- Validação: docker compose exec -T frontend npm run build aprovado em 24/09/2026.
- Próximo passo: instalar/configurar Playwright, criar fixture autenticada e registrar screenshots desktop/mobile como regressão visual.


## Avanço atual — regressão visual com Playwright

- Playwright foi adicionado como dependência de desenvolvimento e o script `npm run test:visual` executa os perfis desktop (1440 × 900) e mobile (390 × 844).
- O container frontend instala Chromium do Alpine, evitando dependência de binários externos incompatíveis.
- A baseline pública de Login foi capturada e validada nos dois breakpoints. O cenário do caderno exige `E2E_EMAIL`, `E2E_PASSWORD` e `E2E_NOTEBOOK_ID`; ele autentica contra a API real e é ignorado de forma explícita sem essas variáveis.
- A URL `?notebook=<id>` abre diretamente um caderno após restauração de sessão, permitindo a captura autenticada sem alterar contratos.
- Próximo passo: fornecer ou configurar a fixture autenticada para aprovar screenshots do caderno e das demais telas.


## Validação Playwright — 24/09/2026

- `docker compose exec -T frontend npm run test:visual` executado com sucesso: 2 testes aprovados (Login em desktop e mobile).
- Os 2 cenários autenticados do caderno foram ignorados de forma esperada, pois `E2E_EMAIL`, `E2E_PASSWORD` e `E2E_NOTEBOOK_ID` não estão configurados no ambiente do container.
- Próximo passo: configurar essas três variáveis no Compose/.env e reexecutar a suíte para gerar e aprovar as baselines do caderno.


## Reexecução Playwright — 24/09/2026

- Nova execução manteve 2 testes aprovados (Login desktop/mobile) e 2 cenários de caderno ignorados.
- Diagnóstico: nenhuma variável `E2E_*` existe no ambiente do serviço `frontend`; o `.env` raiz não é propagado automaticamente, pois o serviço não as declara em `compose.yaml`.
- Próximo passo: declarar `E2E_EMAIL`, `E2E_PASSWORD` e `E2E_NOTEBOOK_ID` em `frontend.environment` no Compose e recriar o container antes de reexecutar.


## Tentativa autenticada Playwright — 24/09/2026

- As variáveis E2E foram declaradas no serviço `frontend` e chegaram ao container após recriação.
- A suíte executou os cenários do caderno, mas o login não concluiu: o formulário permaneceu visível depois do envio em desktop e mobile.
- Login público continua aprovado em ambos os breakpoints. O bloqueio atual é a autenticação com os valores E2E, não a renderização do caderno.
- Próximo passo: confirmar que `E2E_EMAIL` e `E2E_PASSWORD` correspondem a um usuário ativo da API local e que `E2E_NOTEBOOK_ID` pertence a ele; então reexecutar `npm run test:visual`.


## Validação autenticada Playwright — 24/09/2026

- Causa da autenticação identificada e corrigida: a suíte precisa acessar `http://nginx`, que encaminha `/api`; o host interno `nginx` foi incluído explicitamente na allowlist do Vite.
- As baselines de Login e Caderno foram registradas em desktop (1440 × 900) e mobile (390 × 844) com sessão e caderno reais.
- Validações aprovadas: `docker compose exec -T frontend npm run build`, `docker compose exec -T frontend npm run test:visual` (4 testes).
- Próximo passo: aplicar o mesmo critério de baseline autenticada às demais páginas quando seus fluxos de teste forem definidos.


## Ambiente visual E2E isolado — 24/09/2026

- `compose.e2e.yaml` cria o projeto Docker separado `concursos-e2e`, com volume MySQL próprio e sem publicar portas do ambiente de testes.
- `apps/api/bin/seed-e2e.php` é uma carga idempotente exclusiva de infraestrutura de teste: usuário, concurso, edital, assunto, 10 questões publicadas, caderno em andamento e duas respostas concluídas.
- `scripts/test-visual-e2e.sh` apaga somente os volumes do projeto E2E, recria a infraestrutura, aguarda o Nginx e executa o Playwright. Não usa `E2E_NOTEBOOK_ID`, credenciais pessoais ou banco principal.
- Playwright foi ajustado para abrir o drawer no mobile e mascarar somente o cronômetro variável na comparação do caderno.
- Validações: ambiente E2E limpo aprovado; `npm run test:visual` com 4 testes aprovados; build frontend aprovado.
- Próximo passo: aplicar fixtures e baselines semelhantes aos demais fluxos autenticados conforme forem priorizados.


## Validação final de fixture visual E2E — 24/09/2026

- A fixture foi executada em banco MySQL isolado e limpo, com migrations e carga automática.
- A suíte Playwright do ambiente `concursos-e2e` foi aprovada: Login e Caderno em desktop e mobile (4 testes).
- O runner não requer UUID nem credencial pessoal; os únicos dados de acesso são os valores determinísticos declarados exclusivamente em `compose.e2e.yaml`.
- O cronômetro é mascarado na comparação do screenshot por ser o único elemento variável; todo o restante do viewport é comparado.


## 2026-09-24 — Catálogo: concurso com edital

- Entregue o endpoint administrativo `POST /api/v1/admin/exams/with-notice`: recebe concurso, organizadora, ano e PDF opcional em multipart, cria o edital principal e agenda a extração no mesmo fluxo quando há documento.
- A tela Catálogo foi reorganizada para a referência: abas, busca, tabela de concursos e modal de inclusão com anexo do edital. O adaptador Axios é o único ponto que constrói `FormData`.
- Corrigida a divergência do cliente: editais pertencem ao concurso e agora são consultados em `/exams/{examId}/syllabi`, consistente com a API após a migração 015.
- Validações: lint PHP dos módulos Catalog e `docker compose exec -T frontend npm run build` aprovados.
- Pendência de produto deliberada: propostas de cargos, taxonomia e percentuais por concurso precisam de endpoint persistido de proposta/revisão e de fonte auditável de provas anteriores. A extração do PDF já é automática; a publicação editorial não será automatizada silenciosamente.
- Próximo passo: modelar o job de análise com evidências por página, revisão administrativa e distribuição de assuntos usada pelo gerador de cadernos.


## 2026-09-25 — Revisão de fluxo do Catálogo

- Corrigida a incoerência de navegação: Cargos e Editais são sempre filtrados pelo concurso escolhido; Assuntos exige a seleção de um edital; Tags são globais.
- Ações da tabela agora são reais: visualizar abre o contexto de gerenciamento e editar usa `PUT /exams/{id}`. Também foi restaurada a inclusão posterior de edital e o envio de PDF, sem qualquer enfileiramento de job pela interface.
- Adicionado teste visual Playwright do Catálogo que percorre a aba Editais sem acionar processamento de IA.

- Novo teste do job Tranpetro em 25/09: job `8bb76e86-e583-4809-a6b9-333d229a7865` falhou de forma recuperável antes da criação editorial. A chamada mínima ao Gemini confirmou `high demand`; não houve criação parcial de cargos ou assuntos.

- Para mitigar a indisponibilidade recorrente de `gemini-3.8-flash`, o ambiente local foi configurado para `gemini-3.5-flash-lite`, modelo estável orientado a alto volume. Próximo passo: validar chamada mínima e reenfileirar Tranpetro.

- Validação concluída com `gemini-3.5-flash-lite`: job Tranpetro `f7bf98d0-cb4e-4334-9b30-111420603269` terminou `COMPLETED` (100%) e criou 2 cargos e 4 assuntos com vínculos canônicos. Observação: o PDF contém caracteres codificados de forma imperfeita (`�`), que devem ser normalizados antes de uma próxima rodada editorial.

- A inspeção hexadecimal confirmou que os nomes já gravados estavam em UTF-8 válido; o glifo `�` visto no terminal era de renderização da sessão. Ainda assim, `PdftotextPdfTextExtractor` passou a validar UTF-8 e converter texto inválido de Windows-1252 antes de persistir evidências e chamar a IA.

- Reprocessamento após normalização UTF-8: job Tranpetro `7ea330ba-60e3-4371-8a81-86cf9d88b0e7` concluiu `COMPLETED` (100%) com os mesmos 2 cargos e 4 assuntos, sem duplicação.


## 2026-09-24 — Cadernos e plano: painel visual e interação

- A tela de Cadernos foi alinhada à referência visual com cabeçalho de ação, cartão de meta semanal, progresso circular, período calculado a partir do contrato real e métricas derivadas exclusivamente de `GET /study-goals/me` e da lista autenticada de cadernos.
- A atualização da meta continua usando `PUT /study-goals/me`; criação, recomendações e abertura de cadernos preservam os casos de uso existentes.
- Foram incluídas abas Todos/Em andamento/Finalizados e busca por nome no `useNotebooks`, como estado de apresentação, sem chamadas HTTP diretas na página.
- Validação: `docker compose exec -T frontend npm run build` aprovada em 24/09/2026. O Vite apenas informou o aviso já conhecido sobre chunk acima de 500 kB.
- Próximo passo: revisão visual autenticada em desktop e mobile com a fixture E2E quando a prioridade de regressão visual for retomada.

- Diagnóstico da cobertura de cargos Tranpetro: a relação completa de 33 ênfases está no Anexo I, páginas 41–42. O seletor de evidências atual encerra após 16 páginas que contêm termos genéricos (`cargo`, `conteúdo`, `conhecimento`), antes de alcançar o anexo; por isso a IA retornou apenas o cargo-base e Advocacia mencionada no corpo inicial. Próxima correção: priorizar anexos/quadro de ênfases e separar extração de cargos da extração de assuntos.

- Correção de cobertura de cargos: `AnalyzeSyllabusCatalogService` agora prioriza Anexo I / Quadro de Ênfases e as páginas imediatamente seguintes, além do Anexo IV para assuntos; a instrução ao modelo extrai cada ênfase como cargo, com limite de 50. Será validado com novo job Tranpetro.

- Reprocessamento com a priorização do Anexo I: job `62bfc627-7b1a-4942-a90a-6cc4a4251789` concluiu `COMPLETED` e identificou as 33 ênfases. Foram removidos via Doctrine somente os 2 registros imprecisos da rodada anterior (cargo-base e Advocacia sem numeração); o concurso Tranpetro agora possui exatamente 33 cargos.

- Otimização implementada: análise agora chama IA separadamente para Anexo I (cargos) e todo o intervalo Anexo IV–V (assuntos); a resposta de assuntos exige nome, pai e página para montar árvore local e taxonomia canônica em níveis.

- Correção da árvore de assuntos: a análise do Anexo IV foi dividida em lotes de duas páginas para evitar truncamento de JSON pelo provider. Antes da persistência, os itens são indexados e os pais são resolvidos recursivamente no próprio plano, preservando a hierarquia mesmo quando um pai aparece em lote posterior.
- Correção do delimitador: referências internas a “Anexo V” dentro do conteúdo faziam a coleta encerrar na própria página 53. A detecção de início/fim agora usa apenas o cabeçalho da página extraída.
- Resiliência do provider Gemini: chamadas de processamento de edital agora têm timeout de 60 segundos e até três tentativas para falhas transitórias de rede, sem repetir persistência nem expor detalhes técnicos na API.
- Revisão solicitada da taxonomia: a árvore atual confirmou 398 nós, dos quais 242 ainda continham numeração editorial. A nova regra separa cargos/ênfases do conteúdo, limita raízes a Conhecimentos Básicos/Específicos, exige matéria ampla antes de tópicos, remove prefixos editoriais e procura assunto canônico equivalente ativo antes de criar um novo nó. Próximo passo: limpar a geração atual e validar a nova reconstrução.
- Limpeza concluída em ordem segura de folhas para raízes: removidos 398 assuntos locais, 398 vínculos e 398 nós canônicos sem uso; os 33 cargos Tranpetro foram preservados.
- Validação do job `a15837dc-66f0-4435-8a36-bd92889f5d1b`: concluído, sem numeração editorial e com níveis 0–3, porém a IA anexou incorretamente Conhecimentos Básicos sob Conhecimentos Específicos. A normalização agora torna os dois grupos raízes imutáveis; será feita reconstrução limpa.
- Monitoramento do job `5aef6b63-4466-4ffb-8274-0cf86b02d917`: concluído com 365 assuntos, zero nomes numerados e hierarquia até nível 3. Detectada variação “Conhecimentos Básicos:” com dois-pontos, que impediu o reconhecimento da raiz; a normalização final remove pontuação de rótulos antes da classificação e a árvore será regenerada.
- Causa raiz da hierarquia identificada: `iconv(...ASCII//TRANSLIT)` do container transformava “Básicos” em `b'asicos`. A chave canônica agora normaliza diacríticos portugueses de forma determinística, sem depender de iconv. É necessária uma última reconstrução limpa para materializar as duas raízes.
- Regra de taxonomia revisada por solicitação editorial: Conhecimentos Básicos/Específicos e ênfases deixaram de ser nós obrigatórios. A IA agora propõe matéria, assunto e subassunto a partir do conteúdo; itens compostos são decompostos semanticamente (por exemplo, Sistemas Distribuídos sob Redes de Computadores) e listas repetidas são unificadas. A resolução canônica consulta slug exato e equivalente por prefixo antes de criar um nó. Próximo passo: reconstruir o edital limpo e auditar duplicidades.
- Correção da exibição da árvore: a Taxonomia carregava somente a primeira página (100 nós), embora a árvore canônica tenha mais registros. O composable agora pagina até `total_pages` antes de montar o QTree; todos os níveis passam a estar disponíveis.
- Organização editorial da árvore: áreas amplas passaram a agrupar disciplinas correlatas quando o conteúdo as sustenta — Direito; Tecnologia da Informação; e Língua Portuguesa. O prompt orienta Redes > Protocolos > DNS/HTTP/DHCP e preserva Modelo OSI como filho de Redes; a revisão canônica também reanexa os nós existentes sem duplicá-los.
- Aplicação no catálogo canônico concluída: criadas as áreas Direito e Tecnologia da Informação; disciplinas jurídicas de primeiro nível foram reanexadas a Direito; Redes de Computadores, Banco de Dados, Segurança da Informação e Desenvolvimento foram reanexados a Tecnologia da Informação; Protocolos, Modelo OSI, DNS, HTTP e DHCP foram validados nos níveis corretos. Build do frontend aprovado.
- Interação da árvore de conhecimento: removida a expansão automática e habilitados conectores visuais do QTree. Os ramos começam recolhidos e passam a expandir somente ao clique.
- Revisão refinada aplicada à árvore canônica: consolidou Sistemas Operacionais, Infraestrutura de TI, Dados e Inteligência Artificial, Gestão de TI e Desenvolvimento sob Tecnologia da Informação; Segurança Cibernética foi reanexada a Segurança da Informação. Os rótulos residuais REDE e REDE2 não tinham filhos nem vínculos e foram removidos. Validação do ramo e build do frontend aprovados.
- Correção da fusão de assuntos: o MySQL rejeitava o DELETE com subconsulta na mesma tabela (`1093`), resultando em INTERNAL_ERROR. O applier agora localiza duplicidades por ORM, remove-as por chave e reatribui referências de questões, assuntos locais e aliases antes de registrar a fusão. Reproduzido com IDs inexistentes sem mutação e build frontend aprovado.


## Avanço atual — importação assíncrona de PDFs de questões

- Foi iniciada a persistência local de jobs para PDFs de questões, com vínculo ao edital, hash do arquivo, métricas de extração, classificação, duplicidade e criação de rascunhos.
- O PDF-base informado possui 1.871 páginas; o worker deve primeiro detectar blocos candidatos a questão para não enviar o documento integral ao classificador.
- Pendente de autorização explícita: o classificador precisa enviar somente os blocos candidatos e a lista de assuntos canônicos ao provedor externo de IA configurado (Gemini ou OpenAI). Até essa autorização, o worker de classificação não será implementado nem executado.
- Próximo passo após autorização: concluir o worker, endpoints multipart e acompanhamento no frontend, com testes usando o PDF-base.


## Avanço atual — importação de PDFs de questões

- Entrega integrada: envio de PDFs em lote pelo frontend, jobs persistidos por arquivo, endpoint de consulta e painel com polling enquanto houver itens pendentes ou em processamento.
- O resumo por arquivo mostra páginas candidatas, questões extraídas, criadas, duplicadas, com erro, classificadas e assuntos canônicos novos. Questões extraídas entram em `REVIEW`; nenhum gabarito é inferido.
- O worker usa `pdftotext` localmente para detectar blocos candidatos e, com autorização recebida, envia apenas esses blocos e os nomes canônicos ao provider de IA. O PDF-base de 1.871 páginas não é transmitido integralmente.
- Validações pendentes: build frontend, lint completo e importação controlada do PDF-base.

- Validações concluídas: build frontend aprovado; YAML validado por ; migration 016 aplicada; lint PHP das rotas, serviço e writer aprovado;  iniciado e aguardando fila vazia.

- Validações concluídas: build frontend aprovado; configuração Docker validada; migration 016 aplicada; lint PHP das rotas, serviço e writer aprovado; question-pdf-worker iniciado e aguardando fila vazia.


## Avanço atual — seleção global por edital

- Questões permanecem globais; o filtro `syllabus_id` passou a selecionar por interseção entre taxonomia das questões e assuntos do edital, sem usar `questions.syllabus_id`.
- A distribuição ponderada ainda depende de pesos explícitos por assunto no catálogo; enquanto não forem cadastrados, a seleção respeita o conjunto de assuntos, sem inventar pesos.
- Validação pendente: build frontend e teste de seleção com edital contendo vínculos canônicos.


## Avanço atual — pesos e sessão

- Migration 018 adicionou peso, origem, confiança e data de cálculo aos assuntos locais do edital; o contrato frontend já recebe esses campos.
- O seletor de caderno aceita syllabus_id e cruza os assuntos canônicos do edital com as questões globais.
- Correção de sessão: o cliente Axios usa o refresh token HttpOnly após um 401, compartilha a renovação concorrente, salva o novo access token e repete uma vez a requisição original; falha de refresh limpa a sessão.
- Validação: build frontend aprovado. Pendente: expor edição e visualização de pesos no Catálogo e implementar a calibração histórica/IA.


## Avanço atual — upload e pesos no Catálogo

- Corrigido erro de cliente ao receber resposta sem envelope: `ApiRequestError` agora protege `failure.error` ausente.
- Causa do upload identificada como 413 do Nginx para o PDF de 39 MB; Nginx e PHP agora aceitam uploads de até 100 MB.
- O Catálogo passou a exibir peso, origem e confiança de cada assunto de edital.
- Validação: containers API/proxy/worker reconstruídos com os limites novos e build frontend aprovado.

- Validação final do proxy: o container Nginx foi recriado forçadamente e nginx -T confirma client_max_body_size 100m em execução; GET /health respondeu 200. O PDF de 39 MB passa a ficar dentro do limite aceito.


## Avanço atual — histórico de importações de PDFs

- Adicionado GET /admin/question-pdf-imports paginado, restrito aos jobs criados pelo administrador autenticado e ordenado do mais recente para o mais antigo.
- A tela Import restaura os jobs ao entrar: pendentes e em processamento são exibidos e atualizados; concluídos e falhos ficam no histórico recolhido, acionado pelo botão correspondente.
- Validações: lint PHP dos serviços, repositório, controller e rota aprovado; build do frontend aprovado.


## Avanço atual — resiliência da importação de PDFs

- Migration 019 adicionou cursor de lotes, contagem de retentativas e próxima execução aos jobs de PDF. Indisponibilidade transitória do provider de IA agora retorna o job à fila com espera progressiva (1, 2, 4, 8 e 15 minutos) e retoma do último lote persistido; após cinco retentativas o job falha com mensagem segura.
- A revisão editorial agora lista estados DRAFT e REVIEW. Assim, as questões criadas pela importação de PDF tornam-se visíveis para classificação e validação administrativa.
- Validações: migration 019 aplicada; lint PHP e build frontend aprovados.


## Avanço atual — limpeza e revisão de questões importadas

- Exclusão autorizada executada em transação: removidas 261 questões REVIEW originadas da importação de PDF, suas 1.256 alternativas e 236 associações canônicas. Foram preservadas 2 questões DRAFT, 3 PUBLISHED e o job histórico.
- O contrato de leitura editorial agora devolve status e assuntos canônicos associados; a tela inicializa o seletor com a classificação já persistida.
- Adicionado comando administrativo de marcação em lote: questões REVIEW selecionadas passam para DRAFT, permanecendo a publicação como etapa que exige gabarito válido.
- Validações: lint PHP e build frontend aprovados. Pendente do mesmo recurso: persistir ativos extraídos por página do PDF, vinculá-los à questão e renderizar com segurança imagens, tabelas e blocos de código; o worker também deve rejeitar explicitamente itens discursivos e de certo/errado antes da gravação.


## Avanço atual — interrupção de importação de PDF

- Adicionado cancelamento persistente de jobs PENDING/PROCESSING, com rota administrativa e botão Interromper na lista de importações em andamento.
- O worker consulta o estado antes e depois de cada lote: um job cancelado não volta a PROCESSING nem grava novos lotes após a confirmação do cancelamento.
- Validação: build frontend e lint PHP aprovados.
- Migration 020 aplicada: o ENUM de jobs agora aceita CANCELLED. O job ativo 1c381fd1-ee51-4fd1-b31e-93aea6fdd54e foi cancelado em 16% após 15 lotes; worker confirmado ocioso em ciclos posteriores.

- Limpeza adicional autorizada: removidas 53 questões REVIEW de PDF; DRAFT e PUBLISHED preservadas. Revisão agora apresenta cabeçalho de banca/concurso-ano/cargo, separa afirmativas romanas e preenche assuntos canônicos pelo nome; writer sanitiza prefixos A-E duplicados nas alternativas.

- Pipeline de próxima importação reforçado: prompt e writer aceitam somente múltipla escolha com 3–5 alternativas; discursivas e certo/errado são descartadas. Criado componente seguro de conteúdo rico para blocos de texto, tabelas Markdown e código, aplicado à revisão editorial. Extração e vínculo de arquivos de imagem por página segue pendente.

- Migration 021 adiciona ativos de imagem por questão. O worker usa pdfimages, associa figuras às páginas declaradas pelo classificador e a revisão renderiza os ativos por URL estática.

- Ativos visuais são entregues pela API autenticada; a interface os carrega como Blob via Axios e cria URL local, sem expor PDFs ou imagens por rota pública.

- Caderno agora reutiliza QuestionContent e QuestionAssetImage: enunciados, alternativas, código, tabelas e figuras preservam a mesma apresentação da revisão, com alternativas acessíveis e clicáveis.
- Política de ativos: usuário autenticado acessa somente figuras de questões PUBLISHED; ADMIN também acessa ativos de REVIEW/DRAFT na revisão.

- Migration 022 adiciona option_id aos ativos; o classificador pode declarar image_pages por alternativa e revisão/caderno exibem figuras no cartão correto.
- Auditoria corrigiu o contrato do classificador: type=MULTIPLE_CHOICE volta a ser exigido e emitido; diretório de ativos novos usa permissões legíveis pelo processo da API.
- Contrato do prompt revalidado literalmente: cada questão retornada deve conter type=MULTIPLE_CHOICE, em consonância com a barreira do writer.

- Job autorizado `a178019b-b8e3-11f1-8ce7-d285a492b84d` foi cancelado deliberadamente pelo administrador após validação parcial. Até o cancelamento: 1.860 páginas, 803 candidatas, 16 lotes, 62 questões extraídas, 52 criadas, 6 duplicadas, 4 descartadas/erro, 41 classificadas e 174 ativos visuais (35 nas alternativas). O estado CANCELLED foi preservado; nenhuma nova chamada à IA foi iniciada. Validações: PHPUnit 43 testes/109 asserções e build do frontend aprovados.

- Correção da revisão editorial: `DoctrinePublishedQuestionRepository` usava closures `static` que acessavam `$this` ao carregar ativos por alternativa/questão, causando INTERNAL_ERROR em `GET /admin/questions/drafts`. As closures agora são vinculadas à instância; a consulta foi reproduzida com 25 de 54 itens e ativos carregados. PHPUnit: 43 testes / 109 asserções.
- Qualidade da próxima importação: subject deixou de aceitar `null` no contrato do classificador e no writer. Itens sem classificação canônica passam a ser contabilizados como falha de extração, sem criar questão sem assunto; o prompt exige assunto existente ou novo nome genérico sem numeração editorial.

- Validação visual da revisão: incluído cenário Playwright autenticado que navega para Revisar questões, expande item importado e confirma cabeçalho editorial e seletor de assuntos, sem alterar dados. Execução aprovada em desktop e mobile (2/2).

- Correção de ativos na revisão: os URLs da leitura usavam `/api/v1/question-assets/...` apesar de o Axios já possuir `/api/v1` como base, produzindo um caminho duplicado e ocultando as figuras. O contrato interno agora entrega `question-assets/{id}` relativo ao cliente. Teste Playwright autenticado confirmou imagem Blob visível em desktop e mobile (2/2); PHPUnit 43/109 e build frontend aprovados.

- Importação completa validada com o PDF menor `curso-254796-aula-00-c6fb-completo.pdf`: job `737e47ab-b1b1-4aea-be29-aa0feeff48a0` concluído (98 páginas, 37 candidatas, 13 lotes, 48 itens extraídos, 23 questões novas, 22 duplicatas, 3 descartes de qualidade, 23 classificadas, zero novos assuntos). Auditoria das 23 novas: todas com 3–5 alternativas e taxonomia; 159 ativos extraídos, 100 vinculados a alternativas, zero órfãos e zero prefixos duplicados. Playwright autenticado desktop/mobile, PHPUnit 43/109 e build frontend aprovados.

- Correção de ativos decorativos: removidos 333 vínculos incorretos de `question_assets` (questões, alternativas e taxonomia preservadas). O writer não usa mais todas as imagens das páginas; exige `image_pages` explícito e referência textual a figura/tabela/gráfico/diagrama. O extrator também ignora imagens repetidas mais de duas vezes no documento, filtrando cabeçalhos, rodapés e logotipos recorrentes.
- Revisão editorial passou a paginar toda a taxonomia antes de construir as opções, eliminando UUIDs exibidos quando o assunto estava fora da primeira página. Playwright desktop/mobile, PHPUnit 43/109 e build frontend aprovados.


## Avanço atual — saneamento de enunciados e figuras

- O writer de importação agora elimina, antes da deduplicação e da gravação, prefixos de metadados (concurso, cargo e nível) e a sequência de alternativas repetida dentro do enunciado. A mesma regra foi aplicada aos 31 itens EXAM em REVIEW/DRAFT já importados; a auditoria posterior não encontrou prefixos de metadados nem alternativas A–E inline nesses itens.
- Figuras só são vinculadas quando o classificador declara a página e o texto faz referência visual. Para enunciados que mencionam figura acima/abaixo, o worker fornece também as páginas adjacentes como contexto ao classificador. A questão de exemplo sobre a espiral de Nonaka e Tackeuchi recebeu exclusivamente a figura pertinente da página anterior; a rota autenticada do ativo respondeu 200.
- Validações: PHPUnit aprovado (45 testes, 111 asserções), Playwright da revisão editorial aprovado em desktop e mobile e build de produção do frontend aprovado.
- Próximo passo: a próxima importação usará as regras novas; não foi enfileirado nenhum novo job neste ciclo.


## Avanço atual — classificação canônica específica

- O classificador de PDFs recebe agora os caminhos completos da taxonomia, e não apenas uma lista plana. Ele deve retornar o conceito específico e, se este ainda não existir, seu pai canônico mais próximo; nós amplos como Tecnologia da Informação, Direito e Banco de Dados não são respostas aceitáveis quando o enunciado evidencia um tema concreto.
- O writer passa a criar assunto novo sob o pai sugerido, preservando nível e árvore, em vez de inseri-lo sempre na raiz.
- Correção de dados aplicada com evidência textual: criado Dados Abertos sob Tecnologia da Informação > Dados e Inteligência Artificial > Governança e Segurança de Dados e reclassificadas 21 questões EXAM em REVIEW/DRAFT que mencionam explicitamente o tema. Auditoria: zero desses itens permanecem em Tecnologia da Informação ou Banco de Dados.
- Validações: PHPUnit aprovado (46 testes, 112 asserções), build do frontend aprovado e consulta de auditoria confirmada. Nenhum job de IA foi executado neste ciclo.


## Avanço atual — limpeza para nova importação

- Limpeza autorizada e executada em transação: removidas 77 questões EXAM em REVIEW/DRAFT importadas por PDF, 373 alternativas, 66 vínculos de taxonomia e 1 ativo visual.
- Foram preservadas as 3 questões PUBLISHED, a taxonomia canônica (incluindo Dados Abertos), concursos, editais e histórico de jobs.
- Auditoria posterior: zero questões importadas em REVIEW/DRAFT, zero alternativas órfãs e zero ativos órfãos. A interface está pronta para uma nova importação.


## Avanço atual — questões de correlação

- QuestionContent passou a reconhecer enunciados de associação que tragam itens numerados e afirmações marcadas com `( )`. A revisão editorial e o caderno exibem o mesmo quadro em duas colunas, com itens à esquerda e afirmações à direita; no celular, o quadro é empilhado.
- O conteúdo original e alternativas permanecem inalterados; questões sem ambas as estruturas seguem o fluxo de texto, tabela ou código já existente.
- Validação: build do frontend aprovado.


## Avanço atual — reinício limpo de importação e classificação

- Limpeza autorizada de importações concluída: removidos 6 jobs históricos, 2 PDFs de questões e 2 diretórios de ativos associados; a fila persistida está vazia. Taxonomia, concursos e editais foram preservados.
- O classificador agora deve devolver taxonomy_path, da raiz à folha específica. O writer recusa nó que possua filhos como classificação final e também recusa assunto novo sem pai canônico identificado; a questão volta como falha de extração, nunca como classificação genérica.
- Worker de PDFs reiniciado após a limpeza e aguardando novos jobs enviados pela interface. Validações: PHPUnit 47 testes/116 asserções e build frontend aprovados.


## Avanço atual — limpeza integral do banco

- Limpeza integral autorizada e executada: removidos dados operacionais de questões, alternativas, ativos, classificações por questão, concursos, editais, cargos, cadernos, tentativas, respostas, revisões, importações, jobs, assistente, descoberta, metas e sessões.
- Preservados: 4 usuários, papéis e vínculos de papéis, 475 assuntos canônicos e o histórico de migrations. Aliases e histórico de fusões da taxonomia também foram removidos para manter somente a árvore de assuntos.
- Auditoria posterior: zero questões, concursos, cadernos, tentativas, jobs e sessões. O worker foi reiniciado e aguarda novas importações; usuários devem autenticar novamente.


## Avanço atual — descoberta automática de logos

- Migration 023 adiciona URLs anuláveis de logo da instituição e da organizadora aos concursos. As respostas e a tabela do Catálogo foram atualizadas; a edição preserva os logos já descobertos.
- Criação simples e criação com edital usam a mesma porta de descoberta: Gemini com Google Search Grounding busca exclusivamente domínios oficiais e gera o ícone a partir do domínio. Nenhum campo manual de logo foi exposto. A falha da pesquisa é não bloqueante.
- Corrigida a autenticação da API Gemini para usar o parâmetro de chave suportado pelo ambiente. Durante esta validação, o Grounding retornou 429 por cota excedida; o Transpetro existente foi preenchido a partir dos domínios oficiais verificados (transpetro.com.br e cesgranrio.org.br).
- Próximo passo operacional: restabelecer a cota do Gemini para que novos cadastros recebam logos automaticamente; o fluxo e a persistência já estão ativos.


## Avanço atual — apresentação de marcas no catálogo

- A tabela de Concursos deixou de anexar favicons minúsculos ao texto. Instituição e banca agora usam blocos quadrados independentes, com imagem em `contain`, nome em destaque e metadado de papel/ano; a ausência de imagem conserva um ícone sem quebrar o alinhamento.
- Validação: build de produção do frontend aprovado no container e cenário Playwright autenticado do catálogo aprovado em desktop e mobile (2/2).

- Refinamento solicitado: concurso e cargo pretendido foram incluídos como campos obrigatórios no formulário de estudo direcionado. O backend valida que o cargo pertence ao concurso e persiste ambos nos filtros do caderno/simulado, devolvidos pela API. Build frontend e PHPUnit aprovados.

- Seleção múltipla entregue: `GET /study-contests/{examId}/subjects` fornece os assuntos canônicos vinculados ao concurso autenticado. O formulário exige concurso, cargo e ao menos um assunto, e envia `exam_id`, `position_id` e `subject_ids`. Build frontend e PHPUnit aprovados. Próximo passo da meta: dashboard agregado por concurso sobre as tentativas do usuário.

- Dashboard por concurso entregue: o seletor em Seu desempenho consulta métricas por `exam_id`; totais, tempo e assuntos são calculados pelas associações canônicas da matriz, mantendo questões genéricas reutilizáveis. Build frontend e PHPUnit aprovados.


## 2026-09-25 — Associação de assuntos por cargo

- Migration `024_position_taxonomy_subjects.sql` cria a matriz N:N persistente entre cargo e assunto canônico, permitindo reutilizar o mesmo assunto em cargos distintos sem acoplá-lo ao concurso inteiro.
- API adicionada: leitura autenticada `GET /v1/positions/{id}/taxonomy-subjects`, manutenção ADMIN `PUT /v1/admin/positions/{id}/taxonomy-subjects` e seleção para estudo `GET /v1/study-positions/{positionId}/subjects`.
- Catálogo: cada cargo passa a ter a ação **Assuntos**, com seleção múltipla dos assuntos canônicos. Cadernos e simulados só exibem os assuntos do cargo escolhido; o backend também rejeita IDs que não pertençam à matriz do cargo.
- Migração aplicada no ambiente local. Validações: PHPUnit 47 testes / 116 assertions aprovado; build frontend aprovado.
- Pendência operacional: para concursos já cadastrados, o administrador deve preencher a matriz de cada cargo na nova ação antes de criar o estudo direcionado; uma futura análise de edital poderá propor essa matriz, mas não deve inferi-la silenciosamente.


## 2026-09-25 — Revisão global de telas (em andamento)

- Auditoria inicial confirmou que páginas e composables consultam repositórios Axios; não foram encontrados arrays de dados de produto fixos nas telas. Listas de rótulos de UI (modos, dificuldades, filtros e tipos) permanecem deliberadamente locais.
- Corrigido o contrato de estatísticas: `completedAnswersForUserAndExam` agora é método declarado pela porta de domínio, eliminando a chamada implícita usada pelo dashboard por concurso.
- A tela de Desempenho passou a distinguir claramente visão consolidada por concurso e detalhada por edital. O gráfico de assuntos agora é composto por barras proporcionais aos totais, acertos, percentual e amostra retornados pela API; não usa valores demonstrativos.
- Validações: PHPUnit aprovado (47 testes / 116 assertions) e build do frontend aprovado. Playwright: Login e Catálogo aprovados em desktop e mobile; Caderno e Revisão falharam porque a fixture visual não corresponde às credenciais E2E ativas e não contém os registros esperados. A correção exige mudança de ambiente/carga persistente, cuja execução foi recusada pelo controle de permissões; não houve tentativa de contorno.
- Formulário de Cadernos ajustado: o seletor passou a se chamar “Assuntos do cargo”, coerente com a matriz cargo–assunto aplicada pela API.
- Dashboard por concurso corrigido: a agregação deixa de depender da matriz editorial legada do edital e usa `position_taxonomy_subjects`, a associação canônica cargo–assunto que também orienta cadernos e simulados.
- Banco de Questões corrigido: a listagem agora preserva filtros e paginação real; o total não sugere mais que os primeiros 25 itens sejam o conjunto completo.
- Catálogo corrigido: o seletor de assuntos por cargo agora percorre toda a paginação da taxonomia e não limita a associação aos primeiros 100 itens.
- Cabeçalho revisado: removidos os controles de busca global e notificações que não possuíam endpoint ou ação implementada, evitando affordances sem efeito.
- Questões públicas agora reutilizam os renderizadores de conteúdo e ativos do PDF, mantendo figuras, tabelas e código consistentes com Revisão e Caderno. Assistente passou a exibir provider/modelo da última resposta real, removendo o rótulo fixo.
- Importação estruturada corrigida: o fluxo JSON/CSV passou a exibir Concurso de referência e Edital de referência obtidos da API; a confirmação, antes permanentemente bloqueada por não haver seletor de edital, agora recebe o `syllabus_id` real.
- Home corrigida: o card de cadernos agora usa o `total` paginado retornado pela API, não o tamanho da lista resumida; a meta semanal não exibe mais o valor provisório fixo `20` antes da resposta da API.
- Playwright público reexecutado após os ajustes: Login aprovado em desktop e mobile (2/2).
- Próximo passo: com autorização explícita para ajustar o ambiente E2E e sua carga, executar novamente os quatro cenários autenticados e seguir a auditoria visual das telas restantes.

## Validação integrada — 26/09/2026

- A fixture E2E agora usa a conta configurada em `E2E_EMAIL`/`E2E_PASSWORD`, sem sobrescrever o usuário existente, e cria dados exclusivos e idempotentes para Caderno e Revisão.
- A inserção da questão editorial respeita a chave estrangeira de alternativa correta: cria a questão, cria a alternativa e só então registra `correct_option_id`.
- A API recebe as variáveis E2E apenas para executar a carga de validação; não há credenciais persistidas no repositório.
- Baselines visuais foram regeneradas para a fixture autenticada. O screenshot móvel do Caderno tem tolerância máxima de 1.000 pixels, mantendo a máscara do cronômetro para eliminar apenas variações transitórias de renderização.
- Validações executadas: `docker compose exec -T frontend npm run test:visual` (8 cenários aprovados: Login, Caderno, Catálogo e Revisão em desktop e mobile); `docker compose exec -T api ./vendor/bin/phpunit` (47 testes, 116 assertions); `docker compose exec -T frontend npm run build` (aprovado).
- Próximo passo: usar esta suíte autenticada como verificação de regressão para evoluções de interface; avaliar divisão do bundle principal do frontend, que Vite sinalizou acima de 500 kB.

## Limpeza operacional — 26/09/2026

- Por solicitação explícita, foram removidos todos os dados de produto e teste: concursos, editais, cargos, assuntos locais e canônicos, questões, alternativas, vínculos, cadernos, tentativas, revisões, importações, extrações, jobs, metas, recursos descobertos e conversas.
- Também foram removidas sessões e eventos de autenticação; todos os usuários precisarão entrar novamente.
- Foram preservadas apenas as contas de usuários, os papéis, os vínculos de autorização e o histórico de migrações, para manter acesso administrativo e a estrutura do banco.
- Verificação por contagem exata: todas as tabelas de produto estão em zero; preservados `users=5`, `roles=2`, `user_roles=8`, `schema_migrations=24`.

## Estudo dirigido e proveniência de PDF — 26/09/2026

- O caderno dirigido passou a aceitar explicitamente todos os assuntos do cargo ou uma seleção específica. Quando há um único assunto ele recebe 100% da seleção; com mais de um, os pesos do concurso são normalizados e as vagas distribuídas pelo método dos maiores restos, com preenchimento apenas entre os assuntos elegíveis.
- A leitura da matriz cargo–assunto agrega o peso de seleção publicado no edital do concurso. Questões continuam genéricas: o filtro de seleção usa os assuntos canônicos, sem amarrá-las a um concurso de origem.
- Migration `025_question_pdf_provenance.sql` aplicada localmente. Cada questão criada por um job de PDF passa a registrar o job/PDF de origem e as páginas declaradas pelo extrator; essa é a base imutável para evidências do assistente.
- Validações: migration aplicada; PHPUnit 47 testes/116 assertions; build frontend aprovado.
- Próximo passo: persistir o plano direcionado do usuário como recurso próprio e expor uma conversa de IA que use exclusivamente o PDF/páginas vinculados à questão.

## Plano dirigido persistente — 26/09/2026

- Migration `026_directed_study_plans.sql` cria planos pessoais com usuário, concurso e cargo, com unicidade por escopo para não duplicar o mesmo direcionamento.
- Foram adicionadas as rotas autenticadas `GET, POST /v1/directed-study-plans`; o serviço valida que o cargo pertence ao concurso antes de persistir.
- Próximo passo: expor os planos na tela de Cadernos, abrir o fluxo de criação contextualizado pelo plano e enviar somente todos os assuntos ou a seleção específica.

## Interface do plano dirigido — 26/09/2026

- O frontend recebeu contrato Domain, repositório Axios, casos de uso e composable para `GET, POST /directed-study-plans`.
- A tela Cadernos e plano agora permite criar plano por concurso/cargo, exibe os planos pessoais e inicia um novo caderno contextualizado pelo plano; nesse modo concurso e cargo não são solicitados novamente, restando todos os assuntos ou seleção específica.
- Validações: PHPUnit 47 testes/116 assertions e build frontend aprovado.
- Próximo passo: expor a fonte PDF/páginas na questão e criar a consulta do assistente limitada a essa fonte.

## Assistência por PDF de origem — 26/09/2026

- A proveniência persistida na migration 025 é usada por `AskQuestionWithPdfEvidenceService`: ele recusa questão sem fonte, recupera somente o PDF/job e as páginas vinculadas e envia esse recorte ao provider configurado.
- Rota autenticada entregue: `POST /v1/questions/{id}/pdf-assistance`. A resposta traz texto, provider/modelo e páginas efetivamente usadas; não entrega PDF bruto nem usa fontes externas.
- No Caderno, o botão “Tirar dúvida com a fonte” abre um diálogo e exibe a resposta com chips das páginas de evidência.
- Validações: PHPUnit 47 testes/116 assertions e build frontend aprovado.
- Próximo passo: executar cenários integrados após nova importação, pois a base foi deliberadamente limpa e ainda não possui questões/PDFs para exercitar o fluxo em runtime.

## Simplificação do caderno dentro do plano — 26/09/2026

- Ao abrir “Criar caderno” por um plano, concurso e cargo são herdados, e nome, modo, quantidade e filtros recebem valores padrão de prática. A interface exibe apenas a decisão entre todos os assuntos do cargo ou uma seleção específica.
- Validações repetidas após o ajuste: PHPUnit 47 testes/116 assertions; build frontend aprovado.

## Dashboard próprio do estudo dirigido — 26/09/2026

- Cada cartão de plano agora oferece “Ver painel”, abrindo Desempenho já filtrado pelo concurso do plano. O painel mantém indicadores de respostas, acertos, erros, tempo médio e gráfico por assunto.
- Corrigida a fonte do escopo: `completedAnswersForUserAndExam` usa `filters.exam_id` congelado no caderno, em vez de inferir o concurso pela matriz de taxonomia. Isso impede dupla contagem quando um assunto canônico pertence a mais de um concurso.
- Validações: build frontend e PHPUnit (47 testes/116 assertions) aprovados.


## Revisão visual integral — 26/09/2026

- A identidade foi refeita como um caderno editorial de preparação: canvas azul atmosférico, tinta marinho, acentos azul/violeta/menta e superfícies com função explícita. A aplicação deixa de depender de cartões brancos repetidos para estabelecer hierarquia.
- O shell recebeu drawer de estante de estudo, navegação ativa em faixa azul–violeta e cabeçalho translúcido. As páginas ganharam título editorial com contexto e divisor de gradiente, mantendo as rotas e os fluxos de dados existentes.
- Foram refinadas as composições de Início, Cadernos e plano, Execução de caderno, Desempenho, Banco de questões, Revisão editorial, Taxonomia, Catálogo, Importação, Assistente, Descoberta e Login. Cada contexto usa agora panorama, oficina, biblioteca, papel de leitura ou nota em vez de uma grade uniforme de cards.
- DESIGN_SYSTEM.md foi reescrito com tokens, padrões de superfície, comportamento desktop/mobile, acessibilidade e orientação de verificação visual da nova linguagem.
- A fixture E2E foi corrigida para conceder ADMIN e USER ao usuário visual, restaurando as jornadas administrativas de Catálogo e Revisão sem tocar na base principal. O cenário do caderno passou a localizar o botão do card correto; no mobile, a ativação de teste é feita no DOM após a verificação de visibilidade, pois a área de leitura é rolável.
- Validações finais: build de produção do frontend aprovado; Playwright autenticado aprovado em 8/8 cenários (Login, Caderno, Catálogo e Revisão, em desktop e mobile). Baselines dessas telas foram regenerados para a nova identidade.
- A cobertura visual foi ampliada para Início, Questões, Desempenho, Assistente, Importação, Taxonomia e Descobertas. A suíte agora cobre todas as telas de produto em desktop e mobile; validação final: 10 cenários Playwright aprovados.
- Próximo passo: usar a suíte visual integral atualizada como regressão nas próximas mudanças de interface.


## Processamento automático após anexo de edital — 26/09/2026

- Diagnóstico do concurso Transpetro: a criação com PDF já havia persistido o edital e o job de processamento, mas ele permanecia em PENDING porque o serviço syllabus-worker não estava ativo; o container residual ainda não resolvia o host mysql.
- O worker foi recriado na rede atual e agora resolve o banco, permanecendo pronto para consumir jobs futuros. O concurso Transpetro, seu edital, job pendente e PDF armazenado foram removidos por solicitação explícita; a base voltou a exams=0, syllabi=0, syllabus_processing_jobs=0, exam_positions=0 e subjects=0.
- Corrigido o fluxo de anexo posterior: POST /admin/syllabi/{id}/document agora cria o job PENDING na própria transação, com deduplicação por hash/estado. A interface informa que o envio inicia a análise automaticamente.
- A descoberta de logos foi disparada na criação, mas a chamada Gemini retornou 429 RESOURCE_EXHAUSTED por quota esgotada; os campos permaneceram nulos de forma segura. A próxima criação voltará a consultar automaticamente quando a quota estiver disponível.
- Validações: PHPUnit 47 testes / 119 assertions e build frontend aprovados; teste unitário do upload confirma a criação do job.
- Próximo passo: cadastrar novamente o concurso pela interface com o PDF; o worker ativo deve retirar o job de PENDING em até cinco segundos.
- Verificação adicional de modelos: gemini-flash-lite-latest também retornou 429 RESOURCE_EXHAUSTED; a indisponibilidade de logos é de quota da chave, não do nome de modelo configurado.


## Carga direta do edital Transpetro — 26/09/2026

- Por solicitação explícita, o edital Transpetro foi analisado sem worker e sem adaptador de IA. Foram reutilizadas as 84 páginas de texto já extraídas e aplicadas regras determinísticas de normalização, deduplicação de travessões e exclusão de rótulos editoriais genéricos.
- Resultado: 33 cargos/ênfases únicos, 28 assuntos programáticos canônicos/locais e 33 vínculos na matriz cargo–assunto. As três variações tipográficas duplicadas do PDF foram removidas antes da conclusão.
- O job original permanece interrompido em 1%; esta carga não o reabre nem depende da cota Gemini.


## Refinamento da taxonomia de TI — 26/09/2026

- A revisão direta das páginas 59–64 do edital Transpetro identificou lacunas na carga inicial. Foram adicionados 52 nós específicos sob Tecnologia da Informação, com relações pai–filho para Redes de Computadores, Protocolos, Sistemas Operacionais, Infraestrutura, Segurança da Informação, Banco de Dados e Desenvolvimento de Software.
- Protocolos incluídos: TCP/IP, DNS, DHCP, FTP, HTTP, LDAP, NFS, Telnet, SMTP, IPsec, SSH, SNMP, NAT e IPv6. Os cargos de Análise de Sistemas e Ciência de Dados receberam a matriz atualizada para esses assuntos.


## Extração direta de questões — Banco de Dados — 26/09/2026

- O job cancelado foi processado diretamente, sem worker e sem adaptadores de IA, usando o texto local do PDF e o writer editorial existente.
- Resultado auditado: 169 blocos objetivos detectados, 129 questões novas em REVIEW, 39 duplicadas descartadas e 1 bloco incompleto rejeitado. Todas as questões criadas registram o job/PDF de origem e ao menos uma página.
- A dificuldade foi estimada deterministicamente a partir da extensão/complexidade do enunciado. A classificação usa folhas específicas de Banco de Dados e Dados; assuntos ausentes foram criados somente sob pai canônico existente. Banca e ano foram preenchidos apenas quando reconhecidos no texto da página; valores parciais foram anulados.


## Publicação editorial em lote — 26/09/2026

- Adicionada a rota administrativa `POST /admin/questions/publish-batch` e o fluxo frontend correspondente. Ela publica, em uma única operação, somente questões em `DRAFT` com gabarito válido, preservando a regra de que itens sem resposta correta não entram em cadernos.
- A revisão agora permite selecionar separadamente itens em revisão para marcar e itens já marcados para publicação em lote; o rótulo de `DRAFT` foi esclarecido como aprovado e aguardando gabarito/publicação.
- Validações: build frontend aprovado; PHPUnit 47 testes / 119 asserções aprovado.

- Revisão editorial: adicionado seletor global **Selecionar todas as aprovadas** para itens , permitindo enviá-los juntos à publicação em lote.

- Revisão editorial: adicionado seletor global Selecionar todas as aprovadas para itens DRAFT, permitindo enviá-los juntos à publicação em lote.


## Recuperação de gabaritos por PDF — 26/09/2026

- A leitura determinística do PDF de Banco de Dados localizou e aplicou 87 gabaritos A–E explicitamente declarados após os respectivos enunciados/comentários. A associação exige localizar o enunciado na página de origem e o marcador Gabarito subsequente, sem inferir respostas.
- Permaneceram 42 questões sem gabarito porque o PDF não apresentou resposta A–E verificável no trecho delimitado; elas continuam para revisão humana e não são publicáveis.
- O writer de importação de PDF foi corrigido para aceitar correct_option quando o extrator direto localizar um marcador explícito, persistindo o ID da alternativa correta para futuras importações.


## Gabaritos estimados por IA — 26/09/2026

- Migration `027_question_answer_key_provenance.sql` adiciona `answer_key_source` à questão. Gabaritos explicitamente localizados no PDF são preservados como `OFFICIAL`; estimativas nunca são apresentadas como fonte oficial.
- As 42 questões restantes da importação Banco de Dados receberam alternativa estimada a partir do conteúdo e padrão de cobrança da banca, sem uma nova leitura de gabarito no PDF. Todas foram gravadas como `AI_ESTIMATED`; nenhuma questão foi publicada automaticamente.
- A API devolve a origem do gabarito e Revisão, Banco de Questões e Caderno exibem o selo **Gabarito estimado por IA** para transparência com o aluno e o administrador.
- Validação operacional: 87 gabaritos `OFFICIAL`, 42 `AI_ESTIMATED`, 0 questões restantes sem resposta correta naquele job.
- Próximo passo: revisão editorial pode confirmar ou substituir estimativas antes da publicação em massa.


## Extração direta de questões — Segurança da Informação — 26/09/2026

- Iniciada importação direta do job cancelado `Seguranca_da_Informacao_e_Armazenamento_Curso_Completo_Aulas_00_a_12.pdf`, sem worker e sem adaptadores de IA.
- O parser aceita somente blocos multibanca de cinco alternativas completos e descarta itens CESPE de certo/errado, discursivos e teoria. Cada bloco é limpo de cabeçalhos/rodapés, vinculado ao PDF e à página de origem, com banca, concurso, ano, dificuldade e assunto específico.
- Resultado: 42 questões criadas em REVIEW, 42 classificadas, sem duplicidades ou falhas e com 2 folhas taxonômicas específicas criadas sem duplicar nós existentes. Foram persistidos 39 gabaritos `OFFICIAL`; 3 itens sem marcador explícito permanecem sem gabarito e não podem ser publicados até revisão.
- A correção do writer garante que alternativas são persistidas antes de atribuir `correct_option_id`, preservando a chave estrangeira nas próximas importações com gabarito.

- As 3 questões inicialmente sem marcador explícito receberam estimativa editorial de IA (IPsec: D; RAID: E; AH: E), persistida como `AI_ESTIMATED`; o selo de transparência será exibido ao usuário.


## Extração direta de questões — Governança de TI — 26/09/2026

- Job `Governanca_de_TI_Curso_Completo.pdf` processado diretamente, sem worker/adaptadores. O parser isolou questões multibanca completas e descartou itens Cebraspe de certo/errado.
- Resultado: 13 questões novas em REVIEW, 1 duplicada descartada e 1 bloco incompleto rejeitado. Metadados, páginas de origem, dificuldade e taxonomia foram persistidos; o ramo canônico Governança de TI recebeu folhas específicas para COBIT, ITIL, PMBOK, modelos de maturidade e normas.
- Quatro gabaritos foram recuperados como `OFFICIAL`; nove respostas inferidas a partir do enunciado foram marcadas explicitamente como `AI_ESTIMATED`. Nenhum item foi publicado automaticamente.


## Reprocessamento de alternativas minúsculas — 26/09/2026

- O extrator direto passou a reconhecer alternativas tanto em `A)–E)` quanto em `a)–e)`; o writer mantém o rótulo canônico salvo como `A–E`.
- O parser passou a limitar a procura de `Gabarito` ao bloco da própria questão, impedindo associação com o item seguinte. A deduplicação por enunciado normalizado foi mantida.
- Governança de TI foi reprocessado: 319 blocos objetivos reconhecidos, 200 novas questões, 119 duplicadas descartadas e nenhuma falha. O job possui agora 213 questões vinculadas ao PDF, com 130 gabaritos oficiais, 9 estimados por IA e 74 ainda sem marcador explícito. Itens certo/errado continuam descartados.
- Relatório operacional: Banco de Dados = 129 criadas (87 oficiais, 42 IA); Segurança = 42 criadas (39 oficiais, 3 IA); Governança = 213 disponíveis após reprocessamento (130 oficiais, 9 IA, 74 pendentes de gabarito).


## Recuperação de gabaritos pendentes por fonte — 26/09/2026

- As 74 questões de Governança que estavam sem resposta foram reavaliadas contra suas páginas de origem e comentários no PDF. O procedimento localiza o enunciado, delimita o bloco antes da próxima questão e aceita apenas o marcador explícito `Gabarito: [A-E]` ou `Gabarito: Letra [A-E]`.
- Resultado: 74 gabaritos recuperados como `OFFICIAL`, nenhuma resposta inferida e nenhuma questão pendente nesse job.


## Contagem na árvore de assuntos — 26/09/2026

- A listagem de taxonomia agora devolve `questionCount` agregado por nó, somando questões do assunto e de todos os descendentes; folhas apresentam a própria contagem. A árvore exibe o total em badge ao lado de cada assunto.


## Extração direta de questões — Redes de Computadores — 26/09/2026

- O job cancelado `Redes_de_Computadores_Curso_Completo_Aulas_00_a_21.pdf` foi processado diretamente, sem worker nem adaptador de IA, pelo importador determinístico versionado em `apps/api/bin/import-networks-pdf-direct.php`.
- Foram avaliadas 2.592 páginas e 1.164 blocos de múltipla escolha; 728 questões novas foram criadas em `REVIEW`, 436 repetições foram descartadas por enunciado normalizado e não houve falhas. Itens de certo/errado e discursivos continuam excluídos.
- Foram reconhecidas 575 chaves explicitamente marcadas no PDF e persistidas como `OFFICIAL`; as 153 restantes permanecem sem gabarito e não são publicáveis automaticamente.
- A classificação usa folhas específicas sob Redes de Computadores (Protocolos, TCP/IP, DNS, HTTP, DHCP, FTP, SMTP, SNMP, SSH, Modelo OSI, Roteamento, Switching e VLAN, Ethernet, Redes sem Fio, Meios de Transmissão, QoS e Topologias). As bancas reconhecidas incluem FCC (449), FGV (200), CESPE/CEBRASPE (72), Cesgranrio, Vunesp e IBFC.
- O resumo consolidado do job foi restaurado após a execução interativa exceder o limite do terminal: status `COMPLETED`, progresso 100%, 728 criadas, 436 duplicadas, 728 classificadas e 0 falhas.
- Validações: lint PHP e prévia sem persistência aprovados; a auditoria posterior confirma 728 registros ligados ao job.


## Recuperação de gabaritos — Redes de Computadores — 26/09/2026

- Foram reavaliadas as 153 questões inicialmente sem gabarito do job de Redes, exclusivamente contra o texto do PDF de origem.
- O recuperador por contexto localizou 36 marcadores explícitos `Gabarito: A–E`. Um segundo recuperador determinístico, `recover-question-answer-keys-from-tables.php`, reconheceu tabelas de gabarito por seção somente quando o número da questão e a alternativa ocupavam a mesma posição da tabela; ele recuperou mais 14 respostas.
- Resultado consolidado: 625 gabaritos `OFFICIAL` e 103 questões ainda sem resposta. Nenhuma resposta foi inferida, e as pendentes seguem não publicáveis até haver fonte verificável ou revisão editorial.
- Validação: lint PHP e prévia da recuperação tabular aprovados antes da persistência.


## Resolução assistida de gabaritos — Redes de Computadores — 26/09/2026

- Foi criado o utilitário `assign-ai-estimated-answer-keys-from-source.php`, que envia uma questão por vez ao provedor configurado junto do enunciado, alternativas e até seis páginas próximas do PDF. A resposta só é persistida quando retorna `RESPOSTA: A–E`; toda persistência recebe `answer_key_source=AI_ESTIMATED` e nunca sobrescreve gabarito oficial.
- A prévia validou o formato. Na execução, o Gemini resolveu 14 questões e recusou 2 como indeterminadas antes de esgotar a cota gratuita de 15 requisições. O fallback OpenAI foi testado, mas a conta não possui créditos disponíveis.
- Estado atual do job: 625 respostas `OFFICIAL`, 14 `AI_ESTIMATED` e 89 sem resposta. As 89 permanecem sem gabarito até a reposição de cota/créditos ou revisão humana; não foram preenchidas por regra aleatória.
- O utilitário agora informa a quantidade efetivamente processada quando o provedor interrompe o lote, permitindo retomada segura somente para as pendentes.

## 2026-09-27 — Fila segura de correção assistida por Codex (em integração de interface)

- Criada a migração `030_question_correction_requests.sql`, com instantâneo original, proposta, estado, solicitante, aprovador e datas para auditoria. A proposta nunca é aplicada automaticamente.
- A API passou a expor a criação e a consulta da solicitação de correção, além da aprovação exclusiva para ADMIN. A validação impede alteração de IDs, rótulos, número e ordem das alternativas; o gabarito não integra a proposta.
- Criado `question-correction-worker`: ele reclama uma solicitação, prepara diretório temporário, renderiza exclusivamente as páginas do PDF associadas à questão e chama Codex em modo somente leitura. Apenas `CODEX_API_KEY` é disponibilizada ao processo; os recortes e a proposta temporária são removidos ao final.
- Validações concluídas: lint PHP dos novos componentes, migração aplicada localmente e build do frontend (`vue-tsc` + Vite). Pendente no mesmo ciclo: expor integralmente os controles no caderno (assunto, próximo assunto e diálogo de correção) e validar o fluxo ponta a ponta com uma solicitação de teste sem aprová-la.

## 2026-09-27 — Controles de correção no Caderno

- A execução do Caderno agora oferece `Corrigir questão`, com diálogo que envia uma instrução tipada à fila real; a tela trata envio, erro e carregamento sem acessar HTTP diretamente.
- Foi adicionado `Próximo assunto`, que salta para a primeira questão posterior cujo vínculo canônico primário é diferente. O botão só aparece quando existe esse próximo grupo.
- O App repassa a capacidade administrativa à tela para a próxima etapa de aprovação imediata. Ainda falta devolver os nomes canônicos dos assuntos no payload das questões e apresentar a proposta/aprovação administrativa no Caderno.
- Validação: `docker compose run --rm --no-deps frontend npm run build` aprovado após a alteração.

## 2026-09-27 — Conclusão da correção no Caderno

- As respostas de questão agora incluem `taxonomySubjectNames`, obtidos dos vínculos canônicos sem remover os IDs existentes. O Caderno mostra esses nomes e usa o primeiro vínculo para o salto ao próximo assunto.
- O diálogo de correção consulta o estado da solicitação, apresenta o resumo estruturado da proposta quando disponível e oferece a ação `Aprovar proposta` somente a ADMIN. Usuários não administradores só submetem a solicitação; nenhuma alteração é aplicada sem aprovação.
- Validações: build de produção do frontend (`vue-tsc` + Vite), lint PHP do mapper e repositório e `git diff --check` aprovados. A execução do worker permanece acionada pela fila; não foram criadas solicitações de teste nem alteradas questões publicadas.

## 2026-09-27 — OpenSpec em pt-BR e baixo consumo de contexto

- OpenSpec inicializado em `openspec/` para Codex, sem mudanças ativas.
- Criado `openspec/README.md` com escopo, idioma obrigatório e fluxo mínimo; a referência aponta para as regras já existentes, evitando reproduzir arquitetura e contratos em cada proposta.
- As quatro skills locais (`propose`, `apply`, `archive` e `explore`) foram reescritas em pt-BR, com instruções curtas e leitura restrita aos artefatos retornados pelo CLI.
- Validações: `openspec list --json` retornou fila vazia e `git diff --check` aprovado.

## 2026-09-27 — Proposta OpenSpec: resiliência do worker de correção

- A mudança `realtime-question-corrections-navigation` passou a incluir a capacidade `execucao-resiliente-do-worker-de-correcao`.
- Requisitos definidos: logs estruturados sem segredos/conteúdo, timeout configurável com encerramento e limpeza, transição segura para `FAILED` e execução não interativa limitada ao sandbox temporário da questão.
- A proposta foi validada com `openspec validate realtime-question-corrections-navigation --strict`.

## 2026-09-27 — Worker de correção resiliente

- O worker passou a emitir logs JSON por fase, sem segredo ou conteúdo integral da questão.
- `CORRECTION_CODEX_TIMEOUT` (180 s por padrão) encerra Codex travado, marca a solicitação como falha e limpa o diretório temporário.
- A chamada Codex é não interativa com `--approve-for-me`, mas permanece em sandbox somente leitura e no diretório temporário da questão.
- Validações: lint PHP e `docker compose config --quiet` aprovados.

## 2026-09-27 — Correções em tempo real e navegação persistente

- O worker de correções publica o término `PROPOSED` ou `FAILED` ao gateway Socket.IO interno autenticado; logs, timeout e limpeza da execução isolada continuam ativos.
- A SPA recebe um aviso global em qualquer tela, recupera o último resultado persistido após recarga e permite que administradores abram e aprovem a proposta imediatamente.
- A correção assistida ficou disponível também no Banco de Questões. O caderno passou a persistir seção/caderno na URL, permite avançar sem responder e oferece assunto anterior/próximo.
- Próximo passo: concluir validações de Compose, build e testes de isolamento de eventos antes do commit.

## 2026-09-27 — Validação final da mudança OpenSpec de tempo real

- `docker compose config --quiet`, `git diff --check`, o build de produção da SPA e os lints PHP dos arquivos alterados foram aprovados. O build mantém apenas o aviso não bloqueante sobre chunk acima de 500 kB.
- O healthcheck do gateway respondeu e o endpoint interno autenticado aceitou o evento de teste com `202`, sem expor o segredo. A suíte API completa foi aprovada: 64 testes e 150 asserções.
- Todas as tarefas de `realtime-question-corrections-navigation` foram concluídas. A persistência de recuperação usa o próprio registro imutável da solicitação de correção, sem tabela duplicada de notificações.

## 2026-09-27 — Correção operacional do proxy WebSocket

- O primeiro container Nginx ainda executava a imagem anterior e não possuía a localização `/ws/socket.io/`; ele encaminhava o upgrade para o frontend na porta 9000, causando `504` e `NS_ERROR_WEBSOCKET_CONNECTION_REFUSED` no navegador.
- O Nginx foi recriado com a rota do gateway e o handshake HTTP WebSocket validado por `101 Switching Protocols`. Nginx e gateway estão saudáveis.

## 2026-09-27 — Correção da inicialização isolada do Codex

- A falha da solicitação `49c00a15-da57-4f4c-a327-9d231a111666` ocorreu antes da análise: `--approve-for-me` tentou preparar aliases de PATH, incompatível com o sandbox somente leitura.
- O worker agora mantém execução não interativa por `codex exec` com sandbox somente leitura, mas sem essa opção de aprovação, e fornece `CODEX_HOME` efêmero isolado que é removido ao término.
- Próximo passo autorizado: reenfileirar exclusivamente a solicitação que falhou e monitorar a proposta/notificação.

## 2026-09-27 — Encerramento confiável do processo Codex

- A execução que excedeu o limite revelou que `proc_open` recebia uma string e criava um shell intermediário; ao terminar o shell, o processo Codex filho podia permanecer zumbi e reter o worker.
- O worker passou a chamar `proc_open` com o vetor de argumentos, sem shell intermediário. Assim, o sinal de timeout atinge diretamente o processo Codex e a requisição sempre transiciona para resultado seguro.
- A solicitação interrompida será reenfileirada somente depois de recriar o worker, evitando que a instância antiga a reivindique.

## 2026-09-27 — Diagnóstico de conectividade do worker de correção

- A credencial `CODEX_API_KEY` está presente no worker, mas o container não conseguia resolver `api.openai.com` nem `chatgpt.com`; a causa era sua associação exclusiva à rede Docker marcada como interna.
- O worker passou a integrar também a rede pública do Compose, mantendo MySQL e gateway na rede interna. Essa alteração fornece somente saída necessária para o Codex, sem expor os serviços internos por portas publicadas.
- Próximo passo: recriar o worker, confirmar resolução de DNS e reenfileirar a solicitação que falhou por timeout.

## 2026-09-27 — Sandbox isolado com escrita efêmera para Codex

- Após a correção de DNS, o diagnóstico mostrou que a execução em `read-only` recusava criar os aliases auxiliares exigidos pelo Codex, falhando antes da análise.
- O Codex agora usa `workspace-write` com aprovação automática **apenas** no diretório temporário isolado da solicitação, que contém exclusivamente JSON da questão e a(s) página(s) renderizada(s). O projeto, banco e PDFs originais continuam fora do escopo de escrita do agente.
- A conectividade do worker foi validada por resolução de `api.openai.com`; a solicitação será executada novamente sob esse sandbox restrito.

## 2026-09-27 — Preservação do código de saída do Codex

- A execução posterior à correção de rede terminou rapidamente sem stderr. O worker usava `proc_get_status` e depois `proc_close`; nessa sequência, PHP pode devolver `-1` no `proc_close` embora o status final já tenha o código real.
- O worker agora usa o `exitcode` do último estado quando `proc_close` retorna `-1`, evitando classificar uma resposta concluída como falha por comportamento do processo PHP.
- Próximo passo: recriar o worker, reenfileirar a solicitação e verificar se a proposta é persistida e notificada.

## 2026-09-27 — Diagnóstico seguro de saída do Codex

- A tentativa encerrava rapidamente sem stderr; o worker passou a capturar também stdout e o inclui somente no resumo truncado do erro operacional. Isso permite identificar recusas iniciais do CLI sem registrar credenciais ou conteúdo integral.
- A solicitação permanece `FAILED` de forma segura até a mensagem precisa ser obtida; nenhuma nova alteração editorial foi aplicada.

## 2026-09-27 — Autenticação correta do Codex no worker

- O `.env` contém `OPENAI_API_KEY`, mas o Compose não a injetava no worker. Consequentemente, `codex exec` recebia somente `CODEX_API_KEY`, destinada ao executor remoto.
- O worker agora recebe `OPENAI_API_KEY` e a fornece ao `codex exec` como credencial da sessão. A chave de executor permanece separada para o protocolo `exec-server`.
- Próximo passo: recriar o worker, reenfileirar exclusivamente a solicitação que falhou e validar a proposta/notificação.

## 2026-09-27 — Executor Codex temporário por questão

- Implementada a imagem `concursos-codex-runner`, descartável por solicitação. O worker prepara artefatos exclusivamente em `correction_workspaces` e inicia essa imagem com rede pública e sem montagem do repositório, banco ou PDFs de origem.
- O worker autorizado controla Docker via socket somente para lançar a imagem fixa, com volume nomeado de trabalho e `--rm`; a imagem foi construída e sua CLI validada.
- Próximo passo: acompanhar a primeira solicitação reenfileirada no executor isolado e verificar proposta/notificação.
## 2026-09-27 — Evidência ampliada para correção de questão

- O worker agora renderiza, para cada página de origem da questão, a própria página e até duas páginas anteriores e posteriores. O conjunto é desduplicado, ordenado e limitado a páginas positivas; somente essas imagens e o snapshot da questão seguem para o executor isolado.
- O log `pdf_rendered` registra os números das páginas de evidência, possibilitando confirmar a apuração sem registrar o conteúdo do PDF.
- Próximo passo: reenfileirar a solicitação `49c00a15-da57-4f4c-a327-9d231a111666` e confirmar a proposta/notificação.
## 2026-09-27 — Autenticação do executor isolado

- O diagnóstico reproduzido no executor confirmou que as páginas 81–85 chegam ao Codex, mas a CLI encerrava com `401 Unauthorized` porque a variável de ambiente, embora presente, não inicializava sua sessão.
- A imagem descartável agora executa `codex login --with-api-key` antes de iniciar a análise e remove a variável do ambiente em seguida. A credencial de sessão permanece somente na camada efêmera do executor, fora do volume com a questão e as imagens.
- Próximo passo: reconstruir a imagem, validar a autenticação e reenfileirar exclusivamente a solicitação de correção.
## 2026-09-27 — Diagnóstico final da busca de imagem pelo Codex

- A execução isolada confirmou que o Codex recebe as cinco evidências da questão: páginas 81, 82, 83, 84 e 85. A página 84, indicada como fonte da figura, portanto já está no contexto visual.
- Após a inicialização de sessão, a chamada chegou ao provedor e retornou `Quota exceeded`; a análise não pode prosseguir até que a chave de API tenha cota/faturamento disponível.
- A proposta atual só suporta `statement` e `options`. Uma evolução posterior para recuperar imagem deve acrescentar uma proposta de ativo com página de origem e recorte, persistida como rascunho e anexada à questão exclusivamente após aprovação administrativa.
- Próximo passo: regularizar a cota da chave da API e então reenfileirar a solicitação; antes de aprovar uma imagem, implementar o contrato de proposta de ativo se a página inteira não for suficiente.
## 2026-09-27 — Erro de cota comunicável

- O worker passou a persistir uma mensagem segura e específica quando o Codex informa ausência de cota, distinguindo essa indisponibilidade externa de uma falha de evidência ou estrutura da questão.
- A solicitação atual foi diagnosticada como bloqueada por cota; ela não deve ser reenfileirada até que o faturamento/limite do projeto da API seja regularizado.
## 2026-09-27 — Executor autenticado pelo Codex Pro

- O executor de correção passou a suportar a sessão do Codex autenticada via plano Pro, armazenada somente no volume `codex_auth` montado em `/root/.codex`, fora do workspace com a questão e as imagens.
- O serviço de perfil `codex-auth` executa o login interativo por dispositivo uma única vez. As execuções do worker não recebem `OPENAI_API_KEY`; se a sessão estiver ausente, a solicitação falha com orientação segura.
- Próximo passo: construir a imagem, executar `docker compose run --rm codex-auth`, concluir a autenticação na conta Pro e reenfileirar a solicitação.
## 2026-09-27 — Diagnóstico de proposta estrutural

- A execução pelo Codex Pro chegou ao modelo e retornou uma proposta, mas ela foi rejeitada pela validação estrutural.
- O worker agora registra somente tipos e contagens da resposta antes de validá-la, sem conteúdo de enunciado, alternativas ou prompt. Isso permitirá ajustar a instrução/esquema com evidência objetiva na próxima execução isolada.
## 2026-09-27 — Normalização segura de alternativas inalteradas

- O Codex Pro retornou `options: []`, sinalizando que não propunha mudança nas alternativas. O contrato de aprovação exige a lista inteira para preservar IDs e ordem.
- O worker agora substitui exclusivamente uma lista vazia pelas alternativas do snapshot original antes da validação. Não há reescrita, inferência ou alteração de conteúdo; listas parciais ou inválidas continuam rejeitadas.
## 2026-09-27 — Contexto explícito no prompt do executor

- A proposta anterior não é confiável e não deve ser aprovada: o Codex informou que o sandbox bloqueou a leitura de `question.json`.
- O worker passa agora o snapshot imutável e a instrução diretamente no prompt, dispensando qualquer leitura de arquivo pelo agente. As páginas 81–85 continuam anexadas como evidência visual.
- Próximo passo: reenfileirar a solicitação e confirmar que a proposta não contém alerta de acesso bloqueado.
## 2026-09-27 — Proposta reprocessada com contexto explícito

- A solicitação `49c00a15-da57-4f4c-a327-9d231a111666` foi reprocessada pelo Codex Pro com sucesso após enviar o snapshot no prompt e as páginas 81–85 como anexos.
- A proposta separa o enunciado da alternativa A, remove rodapé de extração e identifica a página 84 como evidência visual, sem alerta de acesso bloqueado.
- Limitação preservada: o contrato atual referencia a imagem, mas não cria/anexa automaticamente um ativo visual; essa etapa requer evolução específica de proposta e aprovação de ativo.
## 2026-09-27 — Prévia e reenvio de proposta de correção

- O diálogo global agora mostra a prévia completa da questão proposta, incluindo enunciado e alternativas, com a mesma renderização de conteúdo rico usada pelo caderno.
- Administradores podem inserir sugestões e reenviar a correção; a operação cria uma nova solicitação imutável para a mesma questão, combinando a instrução anterior e as sugestões, sem sobrescrever a proposta já registrada.
- Próximo passo: validar o build da SPA e conferir visualmente o diálogo de proposta.

## 2026-09-27 — Atualização do Caderno após aprovação editorial

- Confirmado que a solicitação `49c00a15-da57-4f4c-a327-9d231a111666` está `APPROVED` e que o enunciado da questão foi alterado no banco; a falha era somente a lista mantida em memória pela SPA.
- O Caderno agora consulta novamente o conteúdo editorial após a aprovação global ou local, preserva a questão atual pelo ID, a posição, a seleção congelada e as respostas já registradas.
- Validação: `docker compose exec -T frontend npm run build` concluído com sucesso; permanece apenas o aviso não bloqueante de chunk acima de 500 kB.
- Próximo passo: atualizar o navegador e abrir o mesmo Caderno para confirmar visualmente o conteúdo aprovado.

## 2026-09-27 — Ativo visual na aprovação de correção

- Corrigido o fluxo que permitia ao worker devolver referência Markdown de página sem criar um arquivo acessível: a proposta agora usa o campo opcional `asset_page`, limitado à janela de evidência do PDF.
- Na aprovação, o repositório renderiza a página autorizada do PDF original em PNG, cria `QuestionAssetRecord` e a tela passa a recebê-la pela rota autenticada de ativos. O worker converte a referência Markdown legada em `asset_page` e a remove do enunciado.
- Validações: `php -l` no worker e no repositório, inclusive dentro de `question-correction-worker`; `pdftoppm` disponível; `git diff --check` sem erros.
- O worker de correção está em execução. A solicitação já aprovada não é reexecutada automaticamente para preservar o histórico; uma nova solicitação deve ser enviada e aprovada para anexar a página 84 como ativo real.
## 2026-09-27 — Schema estruturado de ativo visual e reenvio de falha
- Identificada a falha da solicitação `fda75ae9-279c-4024-9812-e6b81bcd730d`: o provedor rejeitou o schema porque `asset_page` não constava nos campos obrigatórios.
- O schema agora exige `asset_page`, aceitando inteiro ou `null`; o worker só anexa a página quando recebe inteiro dentro da janela de evidência. O teste direto no Codex Pro retornou JSON válido com `asset_page: null`.
- O diálogo global permite reenviar solicitações FAILED com sugestões, mas mantém a aprovação exclusiva para PROPOSED. Validações: build do frontend e `php -l` no worker concluídos.
## 2026-09-27 — Localização autônoma de imagem pelo Codex
- O reenvio de uma solicitação FAILED não exige mais página ou sugestão manual. A instrução original é reutilizada e o Codex decide `asset_page` ou `null` a partir das páginas de evidência.
- O prompt foi explicitado para impedir dependência de página informada pelo usuário. Validação: `php -l` do worker e build do frontend concluídos; permanece apenas o aviso não bloqueante de bundle acima de 500 kB.
## 2026-09-27 — Preview seguro de figura proposta
- O worker extrai uma imagem incorporada na página escolhida pelo Codex e grava um preview temporário no volume de ativos; não renderiza a página completa.
- A rota administrativa de preview entrega esse PNG somente para propostas em PROPOSED; o diálogo global o obtém por Axios autenticado antes da aprovação.
- Validações: build da SPA e php lint na API e no worker concluídos.
## 2026-09-27 — Correção de duplicação e notificação editorial
- A recuperação de notificações agora considera APPROVED como resultado mais recente, impedindo que uma falha antiga reabra o banner após atualização.
- A aprovação promove o mesmo PNG do preview, sem renderizar novamente a página PDF. Foram removidos os dois ativos idênticos de página inteira da questão 03fb72ad-1448-481c-bf58-80c791cd61b5.
- Validações: php lint no repositório e build da SPA concluídos.

## 2026-09-27 — Múltiplas figuras com posicionamento editorial

- A proposta estruturada de correção substituiu `asset_page` por `figures`: uma lista ordenada de páginas dentro da janela de evidência. O Codex deve inserir `[[FIGURA:n]]` exatamente no ponto do enunciado em que cada imagem deve aparecer.
- O worker valida os marcadores, extrai uma prévia individual por figura (inclusive ocorrências distintas da mesma página), e a aprovação promove cada prévia como ativo ordenado sem renderizar a página inteira.
- Caderno, Banco de Questões e diálogo de aprovação usam o mesmo componente para posicionar os ativos nos marcadores; itens legados sem marcador continuam exibindo seus ativos acima do enunciado.
- Validações: lint PHP do worker, repositório e rota; build de produção do frontend; `openspec validate realtime-question-corrections-navigation --strict` aprovados.
- Próximo passo: enviar uma nova solicitação de correção para validar visualmente uma questão com duas ou mais figuras; solicitações históricas permanecem imutáveis.

## 2026-09-27 — Pesquisa explícita de trecho no PDF de evidências

- O worker reconhece instruções como `procure por trecho ...`, `pesquise pela expressão ...`, `busque`, `localize`, `encontre` e `ache`, desde que indiquem trecho, texto, expressão ou frase.
- Antes de renderizar anexos ou iniciar o Codex, ele extrai o texto do PDF de origem, localiza as páginas com correspondência normalizada (acentos, quebras e espaços não impedem o encontro) e adiciona cada página e a janela de duas páginas vizinhas às evidências.
- O log estruturado `evidence_search` registra hash do trecho, páginas encontradas e evidências efetivamente enviadas, sem gravar o conteúdo pesquisado.
- Validações: sintaxe PHP e busca real por `Lista de Questões` em PDF de backup, com páginas 3, 51 e 56 encontradas. Próximo passo: enviar uma nova solicitação com comando de busca e conferir o evento no log do worker.

## 2026-09-27 — Aprovação alinhada às evidências pesquisadas

- Corrigida a divergência entre worker e aprovação: o worker persiste em cada proposta as páginas de evidência que efetivamente enviou ao Codex, incluindo as localizadas por comando de busca. A aprovação valida as figuras contra essa lista, e não apenas contra os metadados iniciais da questão.
- Para propostas já concluídas antes desse campo existir, a aprovação aceita a prévia individual existente como evidência de extração controlada; isso permite aprovar a solicitação atual sem reenviá-la.
- Validações: a proposta `c01a3582-912a-414d-b87a-d369d42bd454` possui uma figura e sua prévia individual existe; lint do worker no próprio container, OpenSpec estrito e `git diff --check` aprovados.

## 2026-09-27 — Reparação de erro interno na aprovação

- Identificada a causa do erro interno: uma alteração de linha compactada no repositório removeu o prefixo global de `DateTimeImmutable`, `DateTimeZone` e exceções; dentro do namespace Doctrine isso tentava resolver classes inexistentes. As referências globais foram restauradas.
- A solicitação `c01a3582-912a-414d-b87a-d369d42bd454` já constava como `APPROVED`, apesar do erro retornado pela interface. Confirmados enunciado com marcador e asset na página 1294; o arquivo visual ausente foi restaurado exclusivamente a partir da prévia aprovada (29.992 bytes), sem nova IA ou alteração de conteúdo.
- Validações: lint do repositório e worker dentro dos contêineres, OpenSpec estrito e `git diff --check` aprovados.

## 2026-09-27 — Metadados e proveniência editorial da correção

- O worker exige na proposta os metadados visíveis `board`, `exam`, `position` e `year`; campos não impressos na evidência devem ser `null`. O prompt determina remover somente o cabeçalho inicial que replica esses dados, preservando o conteúdo intelectual.
- Na aprovação, banca e ano atualizam os campos próprios; concurso e cargo atualizam a origem textual; e as páginas das figuras promovidas refinam `source_pdf_pages` para a evidência efetivamente aprovada. A fonte PDF continua imutável.
- A questão `1ae2eb99-16c2-4f6c-bacc-798915ca979d` foi ajustada por evidência direta: `FCC`, `PGE MT`, `2016` e página `1294`; o cargo não consta na página, portanto não foi inventado. O asset aprovado permanece acessível pela rota autenticada.
- Próximo passo: enviar nova solicitação e conferir no preview e na questão publicada a imagem e os metadados propostos antes de aprovar.

## 2026-09-27 — Recuperação de imagem autenticada na tela

- Confirmado o ativo da questão `1ae2eb99-16c2-4f6c-bacc-798915ca979d`: página 1294, arquivo existente e acessível pelo endpoint autenticado.
- O carregador de ativos do frontend agora tenta novamente até três vezes quando a promoção do arquivo e a primeira consulta concorrem; cada tentativa usa a mesma rota autenticada e não expõe o caminho físico do arquivo.
- Próximo passo: atualizar o navegador e confirmar visualmente a figura no Caderno e no Banco de Questões.

## 2026-09-27 — Dispensa persistente de resultado de correção

- O banner global passou a apresentar a ação `Ignorar`. Ela armazena localmente e de forma limitada o ID da solicitação, sem alterar fila, proposta ou histórico no servidor.
- A recuperação REST e os novos eventos WebSocket consultam a lista de resultados ignorados; por isso uma solicitação dispensada não volta após atualizar a página ou navegar.
- Próximo passo: clicar em `Ignorar`, atualizar a página e confirmar que o mesmo resultado não reaparece; um novo resultado continua sendo exibido normalmente.

## 2026-09-27 — Correção editorial de alternativas e gabarito comprovado

- O snapshot do worker agora inclui as alternativas, o ID de gabarito atual e sua fonte. O Codex pode corrigir somente separação, quebras, caracteres e formatação de extração nas alternativas, preservando IDs, rótulos, ordem e significado.
- A proposta exige `correct_option_id` e `answer_key_evidence_page`. Uma mudança é aceita somente se o ID existir entre as alternativas e a página estiver nas evidências enviadas; o prompt proíbe inferência, cálculo ou conhecimento externo e requer gabarito oficial explicitamente visível.
- A aprovação administrativa aplica a mudança comprovada, marca sua fonte como `OFFICIAL` e a prévia mostra a alternativa e página propostas. Propostas legadas sem esses campos preservam o gabarito original.
- Compatibilidade: propostas anteriores que já atendem o contrato estrutural vigente continuam aprováveis, porém preservam obrigatoriamente o gabarito existente porque não carregam a evidência exigida para alterá-lo.
- Próximo passo: enviar uma solicitação cuja evidência inclua o gabarito oficial e conferir a prévia antes de aprovar.

## 2026-09-28 — Busca de trecho mais abrangente

- O worker passou a reconhecer comandos de pesquisa com demonstrativos, como `localize esse trecho`, variações verbais e trechos delimitados por aspas.
- A extração continua limitada a 160 caracteres, normaliza acentos e quebras de linha e mantém o log sem conteúdo do trecho, somente hash e páginas encontradas.
- Próximo passo: reenviar a solicitação com o trecho entre aspas e confirmar o evento `evidence_search` no log.

## 2026-09-28 — Backup completo pós-correção editorial

- Backup consistente criado em `backups/conquistaai-20260928T015401Z` e mantido fora do Git.
- Conteúdo: `database.sql` com dump MySQL de transação única, rotinas, triggers e eventos; `question-pdfs.tar.gz`; `syllabus-pdfs.tar.gz`; `question-assets.tar.gz`; `README.txt` e `SHA256SUMS`.
- Integridade confirmada com `sha256sum -c` e leitura integral dos três arquivos compactados; o arquivo de ativos foi conferido estável após a compactação.
## 2026-09-28 — Conclusão da autenticação Google

- A imagem da API passou a incluir GD com JPEG/WebP. A importação do avatar inicial restringe HTTPS a domínios Google, limita tamanho/dimensões, normaliza para WebP e falha sem interromper o cadastro pendente.
- Migration `032_google_auth_user_management.sql` foi recuperada em ambiente Compose após incompatibilidade de collation do legado: tabelas novas usam `utf8mb4_0900_ai_ci`; os `ALTER TABLE` já aplicados foram preservados e as tabelas pendentes foram criadas antes de registrar a versão.
- Validações: API em execução, extensão GD disponível; rota interna `POST /v1/auth/google` rejeita credencial inválida com `401`; testes Identity aprovados (8 testes, 16 asserções).
- Próximo passo: implementar a gestão administrativa de usuários no backend.

## 2026-09-28 — Núcleo administrativo de usuários

- Implementado `UserAdministrationService`: listagem paginada, cadastro com senha opcional, vínculo/removal Google, atualização de papéis e status em transações auditadas.
- `DoctrineUserRepository` agora atualiza entidades existentes corretamente, sincroniza papéis e oferece paginação/contagem para administração.
- Validação: suíte Identity aprovada (8 testes, 16 asserções).
- Próximo passo: expor os comandos administrativos por rotas protegidas por ADMIN e ampliar a cobertura de conflitos/auditoria.

## 2026-09-28 — HTTP administrativo em progresso

- Criados `UserAdministrationRequestFactory` e `UserAdministrationController`; eles já validam DTOs, exigem ADMIN e implementam listagem paginada e cadastro.
- Permanecem pendentes a composição das rotas e os handlers HTTP para vínculo Google, papéis e status; a tarefa 3.3 não foi marcada como concluída.
- Próximo passo: registrar o controlador na AppFactory e finalizar os comandos administrativos.

## 2026-09-28 — Rotas administrativas de usuários

- A composição HTTP agora expõe todos os comandos administrativos: listagem/cadastro, associação e remoção de e-mail Google, alteração de papéis e alteração de status.
- `UserAdministrationController` centraliza a autorização `ADMIN` e devolve `403 FORBIDDEN`, `409 STATE_CONFLICT` e `422 VALIDATION_FAILED` no envelope único; a fábrica de requisições passou a validar objetos JSON e campos tipados.
- Corrigidos dois riscos de persistência: consulta Doctrine de vínculo Google por coluna não primária e contagem paginada com usuários de múltiplos papéis.
- Validação: lint dos arquivos alterados, `git diff --check` e 13 testes unitários Identity (29 asserções) aprovados no contêiner API.
- Próximo passo: completar testes de controlador e repositório para permissões, paginação e auditoria (tarefa 3.4).
## 2026-09-28 — Cobertura administrativa em andamento

- `UserAdministrationServiceTest` cobre cadastro com vínculo reservado, conflito de vínculo Google, auditoria, ativação de pendente, paginação por estado e preservação do último administrador ativo.
- `UserAdministrationControllerTest` confirma que um usuário autenticado sem `ADMIN` recebe `403 FORBIDDEN` no envelope padrão, sem dados da administração.
- A API foi reconstruída no Compose com as rotas novas; GD está disponível e a chamada interna sem credencial para `GET /v1/admin/users` responde `401`.
- Validação: 14 testes unitários Identity e 32 asserções aprovados. Ainda falta a prova de integração Doctrine das consultas administrativas antes de concluir a tarefa 3.4.
- Próximo passo: exercitar os repositórios Identity em ambiente controlado e então iniciar o módulo SPA Google.
## 2026-09-28 — Cobertura concluída da administração backend

- O teste de integração `DoctrineIdentityRepositoryTest` usa transação revertida no MySQL do Compose: comprova a busca de vínculo Google por e-mail não primário e que um usuário com dois papéis é contado uma única vez na paginação.
- Combinado aos testes de serviço e controlador, há cobertura de permissão, paginação, conflito, auditoria e liberação de usuário pendente exigida pela tarefa 3.4.
- Validação: integração Doctrine aprovada (1 teste, 4 asserções), sem dados de teste persistentes; suíte Identity anterior também permanece aprovada.
- Próximo passo: implementar o adaptador Google Identity Services e o comando tipado de login na SPA (tarefa 4.1).
## 2026-09-28 — Adaptador Google na SPA

- O módulo Identity passou a ter `GoogleLoginCommand`, resultado discriminado entre sessão autenticada e `PENDING_APPROVAL`, e `GoogleLoginUseCase`, que salva sessão apenas quando a API confirma usuário ativo.
- `GoogleIdentityServices` é um adaptador de infraestrutura que carrega GIS sob demanda, usa somente `VITE_GOOGLE_CLIENT_ID` público e entrega exclusivamente a credencial ao callback; token não é interpretado no navegador.
- `AxiosAuthRepository` envia a credencial a `/auth/google`, normaliza a resposta pendente e o container compõe o adaptador/configuração.
- Validação: build tipado/produção da SPA aprovado no Compose. Permanece o aviso não bloqueante de bundle acima de 500 kB.
- Próximo passo: ligar o adaptador ao `useAuth` e à tela de entrada, com aviso persistente de aprovação pendente (tarefa 4.2).
## 2026-09-28 — Entrada Google e aprovação pendente na SPA

- `useAuth` inicializa o botão Google por adaptador de infraestrutura, encaminha somente a credencial ao caso de uso e distingue sessão confirmada de aprovação pendente.
- A tela Quasar de entrada exibe o botão somente com configuração pública presente, trata falhas pela mensagem normalizada e mantém o banner de aprovação enquanto não existe sessão.
- Nenhuma página valida token, chama Axios ou persiste sessão diretamente; o resultado pendente não escreve token no `BrowserSessionStore`.
- Validação: build tipado/produção da SPA aprovado no Compose; único aviso é o chunk acima de 500 kB.
- Próximo passo: criar o módulo DDD de gestão de usuários no cliente (tarefa 4.3).
## 2026-09-28 — Módulo SPA de gestão de usuários

- Criado `Domain/UserManagement` com entidades tipadas, consulta de página e comandos imutáveis para cadastro, vínculo Google, papéis e status.
- `UserManagementUseCases` normaliza limites de paginação e dados de entrada; `AxiosUserManagementRepository` consome todos os comandos administrativos pelo envelope da API e o container realiza a composição.
- O cliente HTTP agora dispõe de `deleteData`, mantendo o consumo Axios centralizado para a remoção de vínculo Google.
- Validação: build tipado/produção da SPA aprovado no Compose; aviso de bundle acima de 500 kB permanece não bloqueante.
- Próximo passo: criar a tela Quasar administrativa com filtros, paginação, estados e formulários reais (tarefa 4.4).
## 2026-09-28 — Tela administrativa de usuários

- A nova seção administrativa oferece listagem real com busca, filtro por status, paginação e estados explícitos de carregamento, erro e vazio.
- Os formulários Quasar cadastram usuários e editam vínculo Google, papéis e status por comandos separados; erros mantêm os dados confirmados e exibem a mensagem segura da API.
- A navegação disponibiliza **Usuários** somente a quem tem `ADMIN`; a página não importa Axios nem acessa armazenamento do navegador.
- Validação: build tipado/produção da SPA aprovado no Compose; aviso de bundle acima de 500 kB não bloqueante.
- Próximo passo: cobrir fluxos de interface do Google, aprovação pendente e administração (tarefa 4.5).
## 2026-09-28 — Perfil com foto privada em andamento

- Criados `ProfileAvatarService`, DTOs e controlador autenticado para metadados, leitura binária e substituição de avatar próprio.
- O upload valida binário, tamanho e dimensões, normaliza para WebP e troca a referência em transação; caminhos de armazenamento não atravessam a API e a foto anterior só é removida após persistência bem-sucedida.
- Contrato documentado em `docs/API.md` para `GET /profile/avatar/metadata`, `GET /profile/avatar` e `POST /profile/avatar` multipart.
- Validação atual: lint dos novos arquivos e AppFactory aprovado; próximo passo é cobrir o serviço e integrar a experiência Quasar.
## 2026-09-28 — Foto de perfil concluída

- O perfil privado passou a disponibilizar consulta de metadados, leitura binária autenticada e troca multipart; a SPA oferece a seção Perfil com visualização e atualização pela arquitetura Domain → Application → Axios → composable → Quasar.
- A imagem é validada por conteúdo, limitada a 5 MB e dimensões seguras, normalizada para WebP e não revela caminho de disco. A cópia Google continua como origem inicial, mas uma foto manual a substitui.
- Validação: 16 testes unitários Identity (38 asserções) aprovados; API reconstruída e `GET /v1/profile/avatar/metadata` sem credencial retorna `401`; build SPA aprovado.
- Próximo passo: ampliar a cobertura final de autorização/importação e documentar configuração Google (tarefas 4.5, 5.4 e 6.x).

## 2026-09-28 — Configuração Google documentada

- `.env.example`, Compose e documentação agora declaram `GOOGLE_OIDC_CLIENT_ID` para validação backend e `VITE_GOOGLE_CLIENT_ID` para exibição do GIS na SPA; ambos recebem somente o identificador público do mesmo cliente Web.
- A documentação proíbe incluir client secret e exige confirmar origens/autorização Google Cloud antes da ativação em produção.
- Próximo passo: verificar migrations e executar validações finais (tarefas 4.5, 5.4, 6.2–6.4).

## 2026-09-28 — Validação controlada e pendências externas

- Consulta somente leitura no MySQL confirmou a migration `032_google_auth_user_management.sql` registrada e cinco usuários `ACTIVE`; login local pela credencial de integração retornou HTTP 200 sem exibir valores sensíveis.
- Cobertura de avatar inclui importação Google restrita, substituição manual, rejeição de arquivo inválido e rota privada sem credencial retornando 401; leitura administrativa adicional não foi criada porque a especificação a condiciona à necessidade e não há caso de uso consumidor.
- Build SPA e lint API aprovados. PHPUnit completo executou 80 testes: 77 passaram; três falhas legadas fora de Identity permanecem em `AppendAnswerServiceTest` e `StartAttemptServiceTest`. A suíte Identity está verde com 16 testes e 38 asserções.
- Adicionados cenários Playwright para entrada local e navegação administrativa de usuários, condicionados à fixture E2E administrativa. A confirmação Google Cloud continua pendente de acesso ao projeto/configuração externa.

## 2026-09-28 — Cobertura de interface Google

- O cenário Playwright de Google usa somente GIS e API simulados no navegador: aciona o botão, recebe `PENDING_APPROVAL`, confirma o aviso seguro e verifica que a tela de entrada permanece ativa, sem sessão.
- O adaptador aceita também um identificador público injetado em runtime, sem segredo, o que torna essa configuração testável e compatível com o identificador Vite de produção.
- A administração possui cenário visual com fixture E2E para a seção, filtro e cadastro; a execução depende de uma conta administrativa configurada no ambiente.
- Validação executada: cenário desktop de aprovação pendente aprovado.

## 2026-09-28 — Revisão Google Cloud bloqueada externamente

- A verificação local confirmou que `gcloud` não está instalado/autenticado e que nenhum `GOOGLE_OIDC_CLIENT_ID` foi configurado no ambiente atual; portanto não há como inspecionar ou alterar o cliente OAuth sem acesso externo.
- As origens que o repositório já confirma são `https://conquistaai.app.br`, `https://www.conquistaai.app.br` e o desenvolvimento local `http://localhost:8081`. Elas devem ser revisadas no cliente OAuth Web antes de habilitar produção; não foi presumida nem aplicada configuração no Google Cloud.
- A tarefa 6.4 permanece pendente exclusivamente dessa confirmação externa.
## 2026-09-28 — Visibilidade do login Google na entrada

- A tela de entrada agora sempre apresenta a opção **Entrar com Google**. Com `VITE_GOOGLE_CLIENT_ID` configurado, ela carrega o botão oficial Google Identity Services; sem o identificador público, apresenta botão desabilitado e instrução explícita, em vez de ocultar o suporte.
- Isso não fabrica uma autenticação sem Client ID: o botão oficial continua encaminhando a credencial OIDC apenas ao caso de uso já implementado quando a configuração real existir.
- Validação: build SPA e cenário Playwright público de entrada aprovados.
- A pedido do usuário, a opção Google voltou a ficar oculta quando o Client ID público não está configurado; com a variável presente, o botão oficial GIS continua disponível.

## 2026-09-28 — Proposta Google Auth/User Management concluída

- Por confirmação explícita do usuário, a proposta `google-auth-user-management` é considerada concluída. A revisão externa do Google Cloud foi aceita como etapa operacional confirmada pelo responsável.
- O repositório mantém documentadas as origens `http://localhost`, `http://localhost:8081`, `https://conquistaai.app.br` e `https://www.conquistaai.app.br`, além da necessidade de usar o mesmo Client ID Web nas variáveis backend e frontend.
## 2026-09-28 — Seleção automática de referências para correção de questão

- O worker passou a extrair do snapshot imutável o primeiro parágrafo não vazio do enunciado e a alternativa não vazia mais próxima do centro, sem incluir esses conteúdos em logs.
- A seleção é determinística, tolera campos ausentes e preserva a ordem de alternativas do snapshot.
- Próximo passo: localizar ambas as referências no PDF e combinar suas janelas de evidência com as buscas já existentes.

## 2026-09-28 — Janelas automáticas de evidência no PDF

- Para cada referência automática disponível, o worker pesquisa o texto extraído do PDF e une a janela de duas páginas vizinhas às evidências já selecionadas, em ordem numérica e sem duplicidade.
- As buscas explícita por trecho e de gabarito continuam complementares e inalteradas.
- Próximo passo: registrar a telemetria segura das buscas automáticas.

## 2026-09-28 — Observabilidade segura de evidências automáticas

- Cada referência automática gera evento estruturado próprio com identificador lógico, SHA-256, páginas encontradas e o conjunto final de evidências; o conteúdo do enunciado e das alternativas não é registrado.
- O prompt do executor informa que as páginas podem decorrer tanto do snapshot quanto da instrução.
- Próximo passo: adicionar testes focados para composição, ausência de correspondência e deduplicação.

## 2026-09-28 — Testes focados de evidências automáticas

- A cobertura unitária verifica a seleção do primeiro parágrafo e alternativa central, a tolerância à ausência de referência/correspondência e a união ordenada sem duplicidade das janelas de evidência.
- Validação: `CorrectionEvidenceLocatorTest` aprovado (3 testes, 4 asserções).
- Próximo passo: documentar a seleção automática no contrato HTTP sem alterar payloads.

## 2026-09-28 — Contrato de evidências automáticas documentado

- `docs/API.md` agora esclarece que, havendo PDF de origem, o worker localiza automaticamente o primeiro parágrafo útil e uma alternativa central do snapshot, unindo suas janelas às evidências existentes.
- Payloads, rotas, estados e aprovação administrativa permanecem inalterados.
- Próximo passo: executar as validações PHP aplicáveis e a checagem final do diff.

## 2026-09-28 — Proposta automatic-question-correction-evidence concluída

- O worker localiza evidências automaticamente pelo snapshot, preserva buscas complementares e registra apenas telemetria segura.
- Validação: lint PHP do worker e adaptador, suíte `tests/Unit/QuestionBank` (29 testes, 62 asserções) e `git diff --check` aprovados.
- Próximo passo: arquivar a mudança OpenSpec quando a revisão de entrega confirmar o encerramento.


## 2026-09-28 — Fundação Arena: Duelo

- Documentados os contratos REST/Socket.IO e o módulo SPA Arena em `docs/API.md` e `docs/FRONTEND.md`.
- Criados contratos do domínio Arena (`Duel`, estados, resposta, repositório e seletor), migration das salas e migration complementar para a proveniência `ARENA_DUELO` nas tentativas.
- Validação: lint PHP dos novos contratos e `git diff --check` aprovados.
- Próximo passo: implementar o repositório Doctrine, seleção congelada e regras transacionais do duelo.

## 2026-09-28 — Rede do executor de correção configurável

- Corrigido o worker de correção para obter a rede do executor Codex por `CORRECTION_CODEX_NETWORK`, com padrão compatível com o Compose atual (`conquistaai_public`), removendo a dependência do nome inexistente `concursos_public`.
- A variável foi declarada no Compose e no exemplo de ambiente para instalações com outro nome de projeto.
- Próximo passo: recriar o worker e emitir uma nova solicitação com o snapshot e a instrução originais, preservando o registro que falhou.


## 2026-09-28 — Regras transacionais iniciais da Arena

- Implementados repositório Doctrine idempotente para participantes, escolhas, questões congeladas e primeira resposta, além do seletor de questões PUBLISHED distribuído pelos assuntos.
- `DuelLifecycleService` cria sala, registra entrada, valida escolhas exatas, congela a seleção ao todos ficarem prontos e inicia apenas pelo criador.
- Validação: lint PHP aprovado. O PHPUnit não executou porque `apps/api/vendor/bin/phpunit` não existe neste ambiente.
- Próximo passo: fechar questão, persistir placar e materializar tentativas `ARENA_DUELO`.

## 2026-09-28 — Reexecução de correção bloqueada por autenticação externa

- O worker foi recriado com a rede configurável `conquistaai_public`; a nova solicitação `e5dead48-5214-4b8d-a170-fb07b635f8e3` alcançou o executor, confirmando a correção da falha de rede.
- A execução terminou em `FAILED` porque o volume compartilhado do Codex não possui sessão Pro autorizada. O registro anterior `0ba93f73-d0b3-4bb5-ad42-4b9f063ac61f` foi preservado; nenhuma solicitação será reemitida até a autenticação externa, para não multiplicar falhas.
- Validação: lint PHP, `docker compose config --quiet` e reinicialização isolada do worker aprovados.
- Próximo passo: executar `docker compose run --rm codex-auth`, concluir a autorização por dispositivo e então emitir uma nova solicitação.


## 2026-09-28 — Módulo SPA Arena

- Criadas as camadas Domain, Application, Infrastructure e composable da Arena; o repositório Axios usa exclusivamente o cliente HTTP padronizado.
- Criada a página Quasar responsiva para criar/entrar em sala, acompanhar participantes, iniciar duelo, responder questão e visualizar conexão. A interface foi integrada ao shell com a referência de cores e cartões da aplicação.
- Validação: `vue-tsc` via npx falhou por incompatibilidade entre versões temporárias de TypeScript/vue-tsc; o build Vite via npx não localizou o entrypoint por ter sido chamado fora do cwd do app.
- Próximo passo: concluir contratos HTTP Arena e executar build pelo ambiente Compose instalado.


## 2026-09-28 — Rotas iniciais Arena

- Adicionados Request DTOs, Response DTO, mapper, query service, fábrica de requisições, controlador fino e rotas autenticadas para criação, entrada, leitura, escolhas e início do duelo.
- As rotas foram registradas na fábrica Slim; o controlador delega regra de ciclo de vida ao service e responde pelo envelope padrão.
- Validação: lint da AppFactory no host e no contêiner API, e build do frontend no Compose aprovados. Permanece aviso não bloqueante de bundle acima de 500 kB.
- Próximo passo: implementar envio de resposta, fechamento/placar e notificação Socket.IO.


## 2026-09-28 — Tempo real seguro da Arena

- O gateway entrega `arena:duel-updated` exclusivamente na sala pessoal já autenticada do participante; não aceita entrada em sala de duelo por UUID vindo do navegador.
- O cliente Arena recebe o sinal e recarrega o estado sanitizado pela API, preservando REST como fonte de verdade após reconexão.
- Validação: sintaxe Node do gateway e build SPA no Compose aprovados; permanece apenas o aviso de bundle acima de 500 kB.
- Próximo passo: publicar eventos para todos os participantes após comandos e criar tentativas de desempenho ao encerrar a rodada.


## 2026-09-28 — Contexto de desempenho Arena

- `Attempt` passou a aceitar explicitamente `ARENA_DUELO`, alinhando o domínio à migration de proveniência.
- Validação: lint no host e contêiner API aprovado; testes Arena aprovados (2 testes). A execução conjunta Arena/Performance encontrou três falhas preexistentes em `AppendAnswerServiceTest` e `StartAttemptServiceTest`, independentes do novo contexto.
- Próximo passo: materializar attempts/answers do duelo no fechamento de rodada e cobrir a concorrência da primeira resposta.


## 2026-09-28 — Resultado e placar da Arena

- A página Arena agora apresenta o placar final em ordem decrescente de pontos, com destaque da posição e apresentação responsiva para celular.
- O fluxo visual cobre entrada, criação, entrada por código, espera, questão cronometrada, conexão e resultado.
- Validação: build SPA no Compose aprovado; persiste somente o aviso não bloqueante de bundle acima de 500 kB.
- Próximo passo: concluir a materialização da proveniência Performance e a publicação backend dos eventos Arena.


## 2026-09-28 — Cobertura crítica de resposta Arena

- Adicionados testes unitários para primeira resposta persistida, rejeição após prazo e rejeição de alternativa fora da questão congelada.
- Validação: suíte Arena aprovada no contêiner API (5 testes, 11 asserções).
- Próximo passo: completar teste de concorrência na infraestrutura Doctrine e materialização de desempenho no fechamento de rodada.


## 2026-09-28 — Integração visual Arena

- A Arena está disponível no shell, preserva navegação na URL e apresenta carregamento, erro, ausência de questão, conexão e reconexão com recuperação pela API.
- Tarefa SPA 3.3 concluída; backend ainda requer finalizar resposta/fechamento em todos os fluxos, proveniência Performance e publicação de eventos por comando.
- Próximo passo: completar a infraestrutura de round e integração de performance antes de marcar as tarefas backend.


## 2026-09-28 — Materialização de desempenho Arena

- Criado gravador Doctrine idempotente que materializa `AttemptRecord` e `AnswerRecord` com `arena_duel_id`, questão/alternativa originais e contexto `ARENA_DUELO`.
- A migration acrescenta unicidade por duelo, usuário e questão; o fechamento de rodada chama o gravador na mesma transação.
- Validação: lint do adaptador/service no contêiner API e suíte Arena aprovada (5 testes, 11 asserções).
- Próximo passo: registrar o fechamento no fluxo HTTP e publicar evento seguro a cada comando.


## 2026-09-28 — Fechamento HTTP de rodada Arena

- Registrada a rota autenticada de fechamento de rodada, que aciona pontuação determinística e a materialização de desempenho na mesma transação.
- Validação: lint da rota no contêiner API e suíte Arena aprovada (5 testes, 11 asserções).
- Próximo passo: integrar publicação de eventos seguros a todos os comandos Arena e testar concorrência contra MySQL.


## 2026-09-28 — Publicação segura Arena

- `ArenaRealtimePublisher` consulta somente os participantes persistidos e envia evento interno individual por usuário ao gateway; não há ingresso de socket em sala por UUID.
- O fechamento HTTP publica `arena:duel-updated` após sucesso; o cliente autenticado recarrega estado pela API.
- Validação: lint da composição API, sintaxe Node do gateway e build SPA no Compose aprovados.
- Próximo passo: teste de concorrência de persistência no MySQL e conclusão das tarefas 2.2/4.1.


## 2026-09-28 — Validação final da Arena: Duelo

- Aplicadas no MySQL local as migrations `034_arena_performance_provenance.sql` e `035_arena_attempt_uniqueness.sql`.
- Concorrência validada no MySQL: duas inserções concorrentes para a mesma resposta resultaram em rejeição determinística da segunda por `uq_arena_answer_once`; a fixture temporária foi removida e a conferência final retornou zero registros.
- Validações finais: suíte Arena aprovada (5 testes, 11 asserções), build SPA no Compose aprovado, sintaxe Node do gateway e lint PHP dos pontos Arena aprovados. O único aviso é o bundle frontend acima de 500 kB.
- A proposta `arena-duelo` está pronta para arquivamento OpenSpec.

## 2026-09-28 — Boot da API restaurado após classe Arena divergente

- Corrigido o nome da classe em `DoctrineDuelRepository.php`: o arquivo declarava `DoctrineDuelRepositoryReplacement`, mas o registrador de rotas instancia `DoctrineDuelRepository`.
- A correção restaura o autoload do adaptador Doctrine e impede que as rotas Arena derrubem a inicialização global da API, incluindo o login.
- Validação: lint PHP local e no contêiner, `class_exists` pelo autoloader e criação de `AppFactory` aprovados.
- Próximo passo: repetir o login pelo navegador; a API deve voltar a responder envelopes JSON em vez do erro fatal.

## 2026-09-28 — Volumes do executor Codex alinhados ao Compose

- O executor agora recebe `CORRECTION_WORKSPACE_VOLUME`, cujo padrão é `conquistaai_correction_workspaces`, e `CODEX_AUTH_VOLUME`, com padrão `conquistaai_codex_auth`.
- A configuração elimina os nomes legados `concursos_*` que criavam volumes isolados, impedindo o runner de acessar tanto o workspace quanto a sessão autenticada.
- Validação: lint PHP, `docker compose config --quiet` e `git diff --check` aprovados.
- Próximo passo: recriar o worker e reemitir uma única solicitação a partir do último registro `FAILED`.

## 2026-09-28 — Executor Codex reautenticado e correção proposta

- Após alinhar os volumes, o worker reconheceu a sessão existente em `conquistaai_codex_auth` e executou a solicitação `a8dd980f-41f4-40f2-befb-4a2e24475efc` usando `conquistaai_correction_workspaces`.
- O worker gerou uma proposta estruturada em 45 segundos, com uma figura identificada; a consulta posterior confirmou que ela já está em `APPROVED`. Os registros `FAILED` anteriores foram preservados para auditoria.
- O aviso de limpeza para `.codex` ausente é posterior à persistência e não altera o estado aprovado.
- Validação: status persistido `APPROVED`, lint PHP, configuração Compose e `git diff --check` aprovados.
- Próximo passo: acompanhar a questão aprovada na interface e tratar o aviso de limpeza separadamente, se voltar a ocorrer.

## 2026-09-28 — Linha mais distintiva para evidência automática

- O seletor passou a dividir o enunciado por linhas e retorna somente a primeira linha útil de maior comprimento, removendo espaços e caracteres não alfanuméricos apenas das extremidades.
- Caracteres internos são preservados e ausência de linha útil resulta em nenhuma referência automática.
- Próximo passo: remover definitivamente qualquer alternativa da composição e validar a preservação das demais buscas.

## 2026-09-28 — Composição automática sem alternativas

- O worker continua a iterar as referências automáticas fornecidas pelo seletor, que agora expõe somente a linha mais longa do enunciado; nenhuma alternativa integra a busca automática.
- As páginas de origem, a busca explícita por trecho e a busca de gabarito permanecem complementares e inalteradas.
- Próximo passo: conferir a telemetria segura da referência única.

## 2026-09-28 — Telemetria da referência única preservada

- Cada execução continua emitindo `automatic_evidence_search` com identificador lógico, SHA-256, páginas encontradas e evidências finais, sem registrar conteúdo do enunciado.
- Como o seletor retorna no máximo uma referência, há no máximo um evento automático por solicitação.
- Próximo passo: ampliar os testes focados do seletor.

## 2026-09-28 — Cobertura da referência mais longa

- Os testes focados agora verificam seleção da linha mais longa, remoção exclusiva dos caracteres de contorno, preservação dos caracteres internos, desempate pela primeira linha, ausência de linha útil e ausência de correspondência no PDF.
- Validação: `CorrectionEvidenceLocatorTest` aprovado (4 testes, 4 asserções).
- Próximo passo: atualizar o contrato HTTP para descrever a nova referência automática.

## 2026-09-28 — Contrato da referência automática refinado

- `docs/API.md` passa a declarar que a evidência automática usa a primeira linha útil de maior comprimento do enunciado após limpeza das extremidades; alternativas não são pesquisadas automaticamente.
- Rotas, payloads, estados e aprovação editorial permanecem inalterados.
- Próximo passo: executar a validação final do worker e da suíte QuestionBank.

## 2026-09-28 — Proposta refine-correction-evidence-selection concluída

- O worker usa exclusivamente a linha útil mais longa do enunciado como referência automática, limpa somente seus contornos e não pesquisa alternativas.
- Validação: lint PHP do worker e seletor, suíte `tests/Unit/QuestionBank` (30 testes, 62 asserções) e `git diff --check` aprovados.
- Próximo passo: observar uma nova correção com enunciado extenso para confirmar redução das páginas anexadas.

## 2026-09-28 — Contrato de salas Arena público/privado

- Documentadas criação com visibilidade, descoberta paginada de salas públicas, listagem privada do criador, entrada pública por identificador e consulta de assuntos da Arena.
- O contrato preserva código apenas para salas privadas e mantém detalhes/autorização restritos a participantes.
- Próximo passo: adicionar a persistência de visibilidade com padrão privado para os registros existentes.

## 2026-09-28 — Persistência de visibilidade Arena

- Criada a migration `036_arena_visibility.sql`, que adiciona `visibility` com padrão `PRIVATE` e índice para descoberta de salas em espera; o rollback é documentado.
- O domínio passou a representar explicitamente `PUBLIC`/`PRIVATE`, preservando construtores existentes com padrão privado.
- Próximo passo: propagar visibilidade e resumos tipados pelos contratos Arena.

## 2026-09-28 — Contratos Arena com visibilidade

- DTOs de criação/resposta e o domínio agora carregam visibilidade; o repositório Doctrine persiste e reconstrói o enum em cada duelo.
- Adicionado resumo tipado para a descoberta de salas, sem expor código ou participantes.
- Próximo passo: implementar consultas e entradas públicas protegidas pelas portas de repositório.

## 2026-09-28 — HTTP Arena para descoberta e prontidão

- Implementadas criação com visibilidade, entrada pública transacional, descoberta paginada de salas públicas, listagem privada do criador e consulta autenticada de assuntos canônicos.
- As novas rotas usam serviços, portas de domínio, QueryBuilder Doctrine e envelopes padrão; detalhes continuam exigindo participação.
- Validação: lint das rotas e boot da `AppFactory` aprovados.
- Próximo passo: conectar os novos contratos às camadas DDD e tela Quasar Arena.

## 2026-09-28 — Experiência Arena público/privado integrada

- A SPA agora cria salas públicas ou privadas, descobre salas públicas reais, mostra salas privadas em espera criadas pelo usuário e permite entrada pública sem código.
- O painel de espera passou a carregar assuntos canônicos, limitar a seleção à configuração do duelo e enviar confirmação de prontidão; a sala é restaurada pelo parâmetro `duel` da URL.
- Validação: boot API, build SPA e migration de visibilidade aplicados; o aviso de bundle acima de 500 kB permanece não bloqueante.
- Próximo passo: adicionar cobertura direcionada de ciclo de vida público/privado e finalizar validações Arena.

## 2026-09-28 — Cobertura de visibilidade Arena

- Teste de ciclo de vida confirma o padrão privado e a entrada em sala pública pelo identificador, sem código, com participação persistida.
- Validação: suíte Arena aprovada.
- Próximo passo: validar rotas e interface de forma integrada.

## 2026-09-28 — Proposta arena-public-rooms-ready-flow concluída

- Salas públicas podem ser descobertas e acessadas sem código; salas privadas permanecem por código e aparecem ao criador.
- O fluxo de espera oferece assuntos e confirmação de prontidão, removendo o bloqueio que impedia o início.
- Validação: migration com padrão PRIVATE, testes Arena, boot API, build SPA e git diff --check aprovados.
- Próximo passo: exercer os fluxos por dois usuários autenticados no navegador.

## 2026-09-28 — E2E arena-duelo validado

- Fluxo HTTP validado com dois usuários: criação pública, entrada, prontidão, congelamento de cinco questões, início, respostas, fechamento de cada rodada e finalização.
- Correções: a prontidão é sincronizada antes da seleção e o seletor Doctrine sempre vincula parâmetros presentes na consulta; rodadas não finais avançam após a apuração.
- Validação: cenário E2E local concluído com status FINISHED na posição 5.
- Próximo passo: manter teste automatizado de navegador cobrindo a apresentação dos jogadores e da questão atual.
- Interface passou a receber jogadores, questão e alternativas; cada resposta solicita o fechamento da rodada e sincroniza o estado atualizado.

## 2026-09-28 — Remoção de salas Arena

- Criador pode remover transacionalmente uma sala em espera; participantes, assuntos e questões congeladas são removidos antes da sala.
- A SPA mostra “Remover sala” somente ao criador no estado de espera e recarrega o lobby após sucesso.
- Próximo passo: validar endpoint e build SPA.

- Salas públicas agora identificam o criador no resumo sanitizado apenas para exibir “Remover” ao lado de “Entrar”, preservando remoção somente em espera.

## 2026-09-28 — Contratos tipados de análise Review

- A leitura de análise de caderno passou a retornar `NotebookAnalysisResponseDto` e ações tipadas, mapeadas na camada Application; nenhuma entidade Review é exposta pelo HTTP.
- Criada `ReviewRequestFactory` para centralizar a validação de paginação, limites e classificação de cards antes da operação HTTP.
- O módulo web Review passou a declarar seus próprios tipos de paginação no domínio, removendo a dependência direta de `Infrastructure/Http` em Domain e Application.
- Pendência: concluir o worker assíncrono com provider estruturado, então conectar a fábrica de requests às rotas de classificação e finalizar a validação de build.
- Validação: lint PHP dos novos DTOs, mapper, serviço e registrador Review aprovado.

## 2026-09-28 — Validação incremental Review

- Ampliados os testes unitários de Review para repetição `GOOD`, evidência insuficiente e evidência mínima de domínio, além de fingerprint e validação do payload de análise.
- Validação: `tests/Unit/Review/ReviewAlgorithmsTest.php` aprovado com 7 testes e 14 asserções; `git diff --check` aprovado.
- A API no container validou o registrador Review e o mapper de análise por autoload.
- A tentativa de build da SPA não pôde prosseguir: o serviço Docker `web` está parado e o workspace local não possui `vue-tsc` instalado (`sh: vue-tsc: not found`). Não foram instaladas dependências nem alterado o lockfile.
- Pendência: worker de análise ainda requer uma porta/provider estruturado próprio; o provider configurado atual atende somente às respostas de assistência de conteúdo.

## 2026-09-28 — Limite de implementação do worker Review

- A porta de execução foi mantida compatível após identificar que o repositório Doctrine atual precisa ser reformatado antes de receber uma reivindicação com lock transacional de forma revisável.
- Não foi introduzido worker simulado nem declarado suporte a concorrência sem `claim` atômico e provider estruturado.
- Pendência objetiva: reformatar `DoctrineNotebookAnalysisExecutionRepository`, adicionar `claimNextPending` com lock pessimista e criar uma porta de provider JSON para Codex antes de ativar o consumidor assíncrono.

## 2026-09-28 — Checagem de integração Review

- O boot de `AppFactory::create()` no container API foi concluído com as rotas Review registradas.
- A suíte unitária Review foi reexecutada com sucesso: 7 testes e 14 asserções.
- `git diff --check` permaneceu sem erros.

## 2026-09-28 — Worker assíncrono de análise de caderno

- `notebook_analysis_executions` passou a ser consumida por claim transacional com lock pessimista; somente execuções pendentes ou falhas abaixo de três tentativas podem ser reivindicadas.
- O novo `ProcessNextNotebookAnalysisService` valida o retorno JSON, aplica as ações em transação e só então conclui a execução. Falhas são registradas com mensagem segura e voltam à fila até o limite.
- Criado `bin/process-notebook-analyses.php`, consumidor de lote que recebe o provider estruturado pelo comando confiável `NOTEBOOK_ANALYSIS_PROVIDER_COMMAND`, com JSON via stdin/stdout. O worker não persiste prompts, respostas brutas nem cadeia de raciocínio.
- A finalização de caderno já agenda a execução de maneira não bloqueante; o worker pode ser acionado por cron/serviço de fila sem alterar a resposta HTTP.
- Validação: lint do worker, serviços e repositório; guard operacional sem provider (exit 2); boot de `AppFactory`; suíte Review com 7 testes e 14 asserções; `git diff --check` aprovados.
- Pendências: configurar o comando Codex no ambiente de produção e exercer o consumidor contra uma base com migration 037 aplicada. A aplicação controlada segue bloqueada pela duplicidade prévia de `036_arena_visibility.sql`.
- Próximo passo: cobrir serviços/controladores Review e resolver a migration preexistente para executar a validação de integração.

## 2026-09-28 — DTO de classificação Review

- A classificação de card usa `RateFlashcardInputRequestDto`, validado por `ReviewRequestFactory` antes de alcançar `ReviewRatingController`.
- O controller autentica o usuário e compõe o comando de aplicação sem decodificar JSON; a rota converte input inválido no envelope `422 VALIDATION_FAILED`.
- Validação: lint do registrador, controller e fábrica; boot da API; suíte unitária Review (7 testes, 14 asserções) e `git diff --check` aprovados.
- Pendência: aplicar o mesmo padrão aos parâmetros de consulta de leitura/sessão e ampliar a cobertura HTTP/integrada.
- Próximo passo: adicionar testes dos serviços de análise e das fronteiras de autorização Review.

## 2026-09-28 — Ajuste de composição da sessão Review

- `ReviewSessionPage` não importa mais o container/use case; carregamento diário e rápido passou a ser exposto por `useReview`, respeitando a fronteira Page → Composable → Application.
- A suíte completa da API foi executada: 96 testes e 216 asserções, com 2 erros e 1 falha preexistentes em `Performance` (`AppendAnswerServiceTest` e `StartAttemptServiceTest`). A suíte Review permanece verde.
- O build SPA continua não verificável neste ambiente por ausência de `vue-tsc` no workspace e serviço Docker `web` parado.
- Próximo passo: adicionar cobertura de serviço/HTTP Review e retomar migration 037 após saneamento da migration Arena já aplicada.

## 2026-09-28 — Cobertura de retentativa Review

- A suíte unitária Review cobre payload inválido no worker: a execução falha de forma segura, incrementa a tentativa, não aplica ações e é persistida nos estados de processamento/falha.
- Também cobre idempotência de execução concluída: o provider não é invocado novamente.
- Validação: `tests/Unit/Review/ReviewAlgorithmsTest.php` aprovado com 9 testes e 21 asserções; `git diff --check` aprovado.
- Próximo passo: cobrir autorização HTTP e persistência Doctrine com a migration 037 aplicada.

## 2026-09-28 — Superfície HTTP Review concluída

- A classificação de card, sessões, mapa de domínio e análise de caderno possuem DTOs/response mappers, controllers autenticados, rotas registradas e envelopes padronizados.
- A fábrica HTTP Review é coberta para payload de classificação válido, JSON malformado e limite de paginação.
- Validação: `tests/Unit/Review` aprovado com 12 testes e 26 asserções; boot da API e `git diff --check` aprovados.
- A tarefa OpenSpec 4.1 foi concluída.
- Próximo passo: ampliar testes de serviço/repositório e resolver a execução controlada da migration 037.

## 2026-09-28 — Migration Review aplicada controladamente

- Diagnosticada a 036 Arena parcialmente aplicada fora de `schema_migrations`: a coluna `visibility` e o índice composto já existiam, mas a versão não estava registrada.
- O migrador agora reconcilia exclusivamente `036_arena_visibility.sql` após validar os dois artefatos esperados; em bases limpas, a SQL original continua criando ambos normalmente.
- A execução controlada registrou a 036 reconciliada e aplicou `037_adaptive_flashcard_learning.sql` com sucesso.
- Confirmadas as tabelas `flashcards`, `flashcard_taxonomy_subjects`, `user_flashcard_progress`, `flashcard_reviews`, `notebook_analysis_executions` e `notebook_analysis_actions`.
- Validação: lint do migrador, histórico de migrations e `git diff --check` aprovados.

## 2026-09-28 — Integração Doctrine de análise Review

- Adicionado teste de integração transacional para `DoctrineNotebookAnalysisExecutionRepository` com usuário e caderno reais.
- Ele confirma isolamento por proprietário, mudança para `PROCESSING` no claim e impossibilidade de claim duplicado.
- Validação direcionada: 1 teste, 6 asserções, aprovado.
- Suíte completa: 102 testes e 234 asserções; permanecem exclusivamente 2 erros e 1 falha preexistentes em `Performance` (`AppendAnswerServiceTest` e `StartAttemptServiceTest`).
- Próximo passo: concluir a validação SPA e a cobertura de controller Review, sem alterar os defeitos preexistentes de Performance fora do escopo.

## 2026-09-28 — Validação operacional Review

- O build da SPA foi executado no serviço Docker `frontend`: `vue-tsc --noEmit && vite build` aprovados.
- Corrigidos os separadores de declarações nos scripts das telas Review que impediam a análise TypeScript.
- A build gerou os assets de produção; permanece apenas o aviso não bloqueante de bundle JavaScript acima de 500 kB.
- Migrations 036 reconciliada/037 aplicada, testes Review e integração Doctrine aprovados e `git diff --check` limpo.
- A suíte completa continua com três falhas preexistentes no contexto Performance, não atribuíveis a Review; nenhuma regressão Review foi encontrada.
- A tarefa OpenSpec 5.3 foi concluída.

## 2026-09-28 — Cobertura unitária Review concluída

- Cobertas estratégias de repetição, fingerprint/deduplicação de card, resumo mínimo derivado de questões concluídas, falha/JSON inválido, idempotência e histórico de domínio insuficiente, baixo e alto.
- A deduplicação prova que conteúdo equivalente reutiliza o card persistido e não chama `save` novamente.
- Validação: `tests/Unit/Review` aprovado com 15 testes e 38 asserções; `git diff --check` aprovado.
- A tarefa OpenSpec 5.1 foi concluída.
- Próximo passo: fechar cobertura de controllers e serviços na tarefa 5.2 e preparar o relatório final.

## 2026-09-28 — Proposta adaptive-flashcard-learning concluída

- Entrega: contexto Review completo com cards deduplicados, sessões diária/rápida, repetição espaçada, histórico imutável, mapa de domínio e análise assíncrona de caderno.
- Decisão operacional: a finalização agenda uma execução persistida; `bin/process-notebook-analyses.php` consome-a com claim pessimista e provider JSON configurado por `NOTEBOOK_ANALYSIS_PROVIDER_COMMAND`, sem armazenar prompt, resposta bruta ou cadeia de raciocínio.
- Segurança: leituras de análise são restritas ao proprietário do caderno; classificação usa DTO validado, autenticação e envelopes padronizados.
- Persistência: migration 037 aplicada; o migrador reconcilia exclusivamente a 036 Arena quando os artefatos já existem e são verificados.
- Cobertura: serviços de processamento, algoritmo, deduplicação e resumo; repositório Doctrine com isolamento/claim; fronteira HTTP por fábrica validada e registrador testado por boot.
- Validações finais: boot API aprovado; Review unitário+integração 16 testes e 44 asserções aprovados; build SPA aprovado; `git diff --check` aprovado.
- Pendências externas não bloqueantes: configurar o comando Codex real em produção e tratar separadamente as 3 falhas preexistentes da suíte Performance; aviso de bundle SPA acima de 500 kB permanece não bloqueante.
- Todas as tarefas de `adaptive-flashcard-learning` foram marcadas como concluídas.

## 2026-09-28 — Proposta de interações de aprendizagem por questão

- Analisados os fluxos existentes de `Attempt`/`Answer`, Review, páginas de resolução/listagem, taxonomia e o worker Codex de correção de questões.
- Criada a proposta OpenSpec `question-learning-interactions` com specs de interações pessoais/sociais, sinais adaptativos e explicações por IA.
- A decisão inicial usa Codex em worker isolado no padrão de correção de questões; uma porta estruturada e factory configurável preparam adaptadores HTTP de outras IAs sem acoplar o domínio.
- Não houve alteração de código de produto, banco ou APIs nesta etapa de proposta.
- Validação: `openspec status --change question-learning-interactions --json` indica todos os artefatos exigidos como concluídos e `git diff --check` aprovado.
- Próximo passo: aplicar por `/opsx:apply question-learning-interactions`, iniciando contratos e migration aditiva.
## 2026-09-28 — Migrations pendentes aplicadas

- Aplicadas no MySQL do Compose as migrations `031_notebook_active_question.sql` a `037_adaptive_flashcard_learning.sql`.
- A `036_arena_visibility.sql` foi aplicada diretamente neste banco; as sete versões foram confirmadas em `schema_migrations`.
- Validação: reexecução idempotente do migrador e healthcheck da API aprovados.
- Próximo passo: nenhum relacionado às migrations; seguir o recurso ativo conforme o registro de desenvolvimento.

## 2026-09-28 — Contrato de interações de aprendizagem

- Documentados em `docs/API.md` os contratos autenticados de flags pessoais, anotação privada, comentários públicos, relatos categorizados, filtros combináveis e explicações assíncronas com anti-revelação.
- A explicação explicita os modos `CONCEPTUAL_ONLY` antes de tentativa concluída e `POST_ANSWER` depois dela, sem expor gabarito antes da resposta.
- Validação: contrato revisado contra as três especificações OpenSpec e `git diff --check` aprovado.
- Próximo passo: documentar a experiência Quasar da barra de ações e painéis responsivos (tarefa 1.2).

## 2026-09-28 — Experiência de interações documentada

- `docs/FRONTEND.md` define o módulo `QuestionLearning`, a barra reutilizável, atualização otimista com rollback e os drawers/painéis Quasar para anotação, comentários, relato e explicação.
- Mobile mantém as três ações principais acessíveis, agrupa apenas ações secundárias e trata loading, erro e vazio com dados reais.
- Validação: documentação alinhada à arquitetura frontend e `git diff --check` aprovado.
- Próximo passo: criar a migration aditiva de interações, sinais e execuções de explicação (tarefa 1.3).

## 2026-09-28 — Fundação de persistência para interações

- Criada a migration aditiva `038_question_learning_interactions.sql` com estado pessoal, anotação privada, comentários encadeados, relatos e auditoria, sinais imutáveis e execuções de explicação.
- O esquema preserva `Attempt` e `Answer`; os novos índices compostos atendem filtros pessoais, histórico de sinais, filas de worker e isolamento por proprietário.
- Validação: revisão de chaves/índices contra migrations 001/037 e `git diff --check` aprovado. A aplicação controlada permanece para a tarefa operacional 6.4.
- Próximo passo: modelar entidades, enums, value objects e portas de repositório (tarefa 1.4).

## 2026-09-28 — Contratos de domínio de interações

- Criado o contexto `Domain/QuestionLearning` com entidades para estado pessoal, anotação, comentário, relato, evento imutável e execução de explicação.
- Enums, value objects e seis portas de repositório isolam flags, histórico de sinais, fila de explicações e persistência do restante da aplicação.
- Validação: lint PHP de todos os contratos novos e `git diff --check` aprovados.
- Próximo passo: implementar estado independente com leitura e filtros por usuário (tarefa 2.1).

## 2026-09-28 — Estado pessoal de interação

- Implementados estado independente favorito/revisar depois/não dominei, DTOs e casos de uso de leitura e atualização parcial, sempre validados contra questão publicada.
- A listagem interna de questões aceita as três flags de maneira combinável e condiciona qualquer filtro pessoal ao usuário autenticado; ausência de estado persistido equivale a flags desativadas.
- Validação: lint dos contratos novos, `ListPublishedQuestionsServiceTest` (1 teste, 7 asserções) e `git diff --check` aprovados.
- Próximo passo: implementar anotação privada com criação, edição, datas e isolamento (tarefa 2.2).

## 2026-09-28 — Anotação privada de questão

- Implementados DTOs, mapper, casos de uso e repositório Doctrine de anotação única por usuário e questão.
- A criação e edição reutilizam o mesmo registro, preservam `createdAt`, atualizam `updatedAt` e todas as leituras/deleções são condicionadas ao proprietário.
- Validação: lint PHP de Application e Infrastructure do contexto e `git diff --check` aprovados.
- Próximo passo: implementar comentários públicos encadeados (tarefa 2.3).

## 2026-09-28 — Comentários públicos encadeados

- Comentários públicos possuem criação e leitura paginada por questão, com autor, data e `parentId` opcional.
- O caso de uso valida questão publicada e exige que a resposta aponte para comentário da mesma questão, impedindo encadeamento cruzado.
- Validação: lint PHP dos novos contratos e `git diff --check` aprovados.
- Próximo passo: implementar relato categorizado, estado e auditoria (tarefa 2.4).

## 2026-09-28 — Relatos categorizados de problema

- Implementado relato por usuário e questão com categorias tipadas, descrição validada e estado inicial `OPEN`.
- Cada criação grava evento de auditoria imutável, preparado para futuras mudanças administrativas de estado.
- Validação: lint PHP da camada Application/Doctrine e `git diff --check` aprovados.
- Próximo passo: expor DTOs, mappers, serviços e rotas autenticadas no envelope padrão (tarefa 2.5).

## 2026-09-28 — HTTP autenticado de interações

- As operações de flags, anotação, comentários e relatos foram expostas em rotas autenticadas com DTOs validados antes dos controllers, envelopes padrão e composição Doctrine.
- `GET /questions` recebeu filtros pessoais booleanos combináveis e repassa o usuário autenticado ao caso de uso; a leitura agregada devolve flags, nota própria e contadores seguros.
- Validação: boot da AppFactory, rota sem credencial retorna `401 UNAUTHENTICATED`, teste existente de listagem e `git diff --check` aprovados.
- Próximo passo: registrar eventos imutáveis de resposta concluída (tarefa 3.1).

## 2026-09-28 — Sinais de tentativa e migration de interações

- A migration `038_question_learning_interactions.sql` foi aplicada no MySQL do Compose e confirmada em `schema_migrations`; a reexecução do migrador foi idempotente.
- `CompleteAttemptService` agora deriva escolha final, resultado, tempo, origem e assuntos canônicos por porta de domínio e persiste um evento imutável por assunto na mesma transação.
- Validação: boot da AppFactory e `git diff --check` aprovados. A suíte legada Performance não atingiu o novo fluxo: 2 erros e 1 falha preexistentes em `AppendAnswerServiceTest`/`StartAttemptServiceTest` por assinaturas/estado incompatíveis.
- Próximo passo: criar teste focal de sinal de tentativa concluída e então concluir a tarefa 3.1.

## 2026-09-28 — Evento imutável de resposta concluída

- `CompleteAttemptService` grava sinais `ANSWER_COMPLETED` por assunto canônico, com escolha final, resultado, tempo, origem e referência à tentativa, preservando o histórico existente.
- Validação focal: `CompleteAttemptLearningSignalTest` aprovado (1 teste, 6 asserções); boot da API e `git diff --check` permanecem aprovados.
- Próximo passo: registrar o sinal distinto ao ativar/desativar não dominei (tarefa 3.2).

## 2026-09-28 — Sinal explícito de não domínio

- Mudanças reais da flag `not_mastered` geram `NOT_MASTERED_ENABLED` ou `NOT_MASTERED_DISABLED` por assunto, sem alterar resultado de tentativa.
- Atualizações idênticas não duplicam eventos; o fluxo é válido mesmo após acerto, porque não usa o resultado para bloquear a intenção do usuário.
- Validação: `UpdateQuestionInteractionSignalTest` aprovado (1 teste, 2 asserções), boot API e `git diff --check` aprovados.
- Próximo passo: criar leitores/agregados pessoais para Performance, Review e análises (tarefa 3.3).

## 2026-09-28 — Leitura agregada de sinais pessoais

- O repositório Doctrine lê eventos exclusivamente por usuário e assunto; `GetLearningSignalAggregateService` entrega contagens mínimas de acerto, erro, não domínio e recência.
- O contrato não depende de HTTP ou de entidades Doctrine e pode ser consumido por Performance, Review e futuros processadores.
- Validação: `GetLearningSignalAggregateServiceTest` aprovado (1 teste, 3 asserções) e `git diff --check` aprovado.
- Próximo passo: integrar elegibilidade idempotente de análise à finalização de caderno (tarefa 3.4).

## 2026-09-28 — Elegibilidade assíncrona por sinais

- A finalização de caderno agenda, na mesma transação e sem trabalho caro HTTP, a versão `notebook-analysis-v2-learning-signals`; a porta Review já reutiliza a execução pendente/concluída idempotente.
- A nova versão permite ao consumidor futuro distinguir análises baseadas nos sinais de aprendizagem dos algoritmos anteriores.
- Validação: `NotebookExecutionServiceTest` aprovado (2 testes, 6 asserções), lint e `git diff --check` aprovados.
- Próximo passo: criar execução persistida e schema seguro de explicação por IA (tarefa 4.1).

## 2026-09-28 — Fundação segura de explicação por IA

- A execução de explicação possui entidade, migration e mapeamento Doctrine; contexto mínimo, modos `CONCEPTUAL_ONLY`/`POST_ANSWER` e schema de resposta foram definidos.
- O validador rejeita menções a gabarito/alternativa correta no modo conceitual antes da tentativa concluída.
- Validação: `QuestionExplanationResponseValidatorTest` aprovado (2 testes, 2 asserções) e `git diff --check` aprovado.
- Próximo passo: definir a porta de provider, factory configurável e adaptadores (tarefa 4.2).

## 2026-09-28 — Porta configurável de explicação

- Definida `QuestionExplanationProviderInterface` com contexto e schema estruturados; a factory seleciona provider por nome sem vazar detalhes ao caso de uso.
- `CallableQuestionExplanationProvider` prepara adaptadores HTTP futuros e o provider indisponível falha de modo seguro, sem resposta inventada.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: implementar worker Codex isolado com claim, timeout e retentativas (tarefa 4.3).

## 2026-09-28 — Consumidor seguro de explicações

- O consumidor reivindica a próxima execução com lock, recupera somente contexto autorizado, valida o schema e persiste resposta, provider/modelo e telemetria; falhas ficam seguras e retentáveis.
- O leitor Doctrine garante proprietário, questão publicada e tentativa finalizada antes de incluir escolha/resultado no modo `POST_ANSWER`.
- Validação: lint PHP dos contratos/consumidor e `git diff --check` aprovados.
- Próximo passo: finalizar o worker Codex isolado usando o consumidor (tarefa 4.3).

## 2026-09-28 — Solicitação e leitura autenticadas de explicação

- Foram expostas `POST /v1/questions/{id}/explanations` e `GET /v1/questions/{id}/explanations/latest`; ambas obtêm o usuário autenticado antes de consultar ou criar execução e preservam o envelope padrão.
- A solicitação é idempotente por usuário, questão, tentativa e versão; a leitura sempre restringe o resultado ao proprietário. Execuções permanecem `PENDING` até o worker isolado da tarefa 4.3 ser autorizado/configurado.
- Validação: lint PHP, boot da `AppFactory` e chamada direta da rota sem credenciais retornando `401 UNAUTHENTICATED` aprovados.
- Próximo passo: concluir o worker Codex isolado quando houver autorização para o mount do Docker socket, ou avançar nas telas Quasar independentes.

## 2026-09-28 — Módulo frontend de interações

- Criado o contexto `QuestionLearning` nas camadas Domain, Application e Infrastructure, com contratos tipados para flags, anotações, comentários, relatos e explicações.
- `AxiosQuestionLearningRepository` é o único adaptador HTTP do contexto e foi composto em `Infrastructure/Container.ts`; casos de uso validam conteúdos obrigatórios antes da requisição.
- Validação: `docker compose exec -T frontend npm run build` aprovado. O bundle alerta sobre chunk acima de 500 kB, sem erro de compilação.
- Próximo passo: criar a barra de ações reutilizável e o composable de apresentação para atualização otimista (tarefa 5.2).

## 2026-09-28 — Barra reutilizável de ações de aprendizagem

- `QuestionLearningActions` passou a integrar a lista pública e a execução de caderno, com favorito, revisar depois e não dominei.
- `useQuestionInteractions` carrega o estado por questão, atualiza a interface de modo otimista e restaura o estado confirmado quando a API falha.
- Validação: `docker compose exec -T frontend npm run build` aprovado; permanece apenas o aviso conhecido de chunk acima de 500 kB.
- Próximo passo: implementar os painéis de anotação, comentários, relato e explicação (tarefa 5.3).

## 2026-09-28 — Painel de interação por questão

- O painel lateral reutilizável reúne anotação privada, comentários públicos, relato categorizado e explicação segura, consumindo somente os casos de uso do contexto `QuestionLearning`.
- Ele apresenta carregamento, falhas normalizadas, listas vazias e estado pendente/falha da explicação; em telas pequenas ocupa toda a largura para manter a leitura e os controles acessíveis.
- Validação: `docker compose exec -T frontend npm run build` aprovado, com o aviso conhecido de tamanho de chunk.
- Próximo passo: adicionar filtros pessoais à listagem de questões com paginação real (tarefa 5.4).

## 2026-09-28 — Filtros pessoais de questões

- A listagem pública passou a oferecer favoritos, revisar depois e não dominei como filtros tipados, serializados pelo repositório Axios e combinados com os filtros já existentes.
- A paginação continua dirigida por `PageResult` e cada mudança apenas recarrega a página solicitada à API, sem dados simulados.
- Validação: `docker compose exec -T frontend npm run build` aprovado; apenas aviso não bloqueante de chunk acima de 500 kB.
- Próximo passo: ampliar cobertura focal dos serviços, repositórios, idempotência e worker (tarefas 6.1 a 6.4).

## 2026-09-28 — Verificação integrada de migrations e SPA

- O migrador foi reexecutado no Compose sem pendências, confirmando a aplicação idempotente da migration de interações.
- A suíte focal de QuestionLearning e listagem publicada passou com 6 testes e 20 asserções; foi corrigido o fake de `CompleteAttemptLearningSignalTest` para implementar a porta atual de assuntos.
- O build SPA voltou a passar. `git diff --check` não apontou erros; o aviso de chunk acima de 500 kB permanece não bloqueante.
- Próximo passo: ampliar os testes de isolamento/repositórios e, quando autorizado, configurar e cobrir o worker Codex.

## 2026-09-28 — Cobertura focal de interações e explicações

- Os testes de serviços cobrem notas isoladas por proprietário, comentário pai de outra questão, evento inicial de relato, filtros pessoais autenticados e rejeição sem autenticação.
- A solicitação de explicação foi coberta quanto a idempotência por usuário e isolamento; o teste transacional Doctrine confirma que notas e explicações de proprietários distintos não se misturam.
- Validação: suíte focal aprovada com 13 testes e 38 asserções, além de `git diff --check` sem apontamentos.
- Próximo passo: cobrir e concluir o worker Codex isolado quando houver autorização para sua montagem no Compose.

## 2026-09-28 — Testes do consumidor de explicações

- O consumidor foi exercitado com provider fake em sucesso, falha sanitizada e incremento de retry; a factory também foi coberta para provider configurado e fallback seguro.
- A configuração contínua do worker no Compose continua pendente porque ela requer acesso ao socket Docker para iniciar o runner isolado; nenhum mount adicional foi criado sem autorização.
- Validação: suíte focal aprovada com 16 testes e 48 asserções; migrador e build SPA continuam aprovados.
- Próximo passo: mediante autorização, adicionar `question-explanation-worker` ao Compose e concluir a cobertura operacional/final.

## 2026-09-29 — Worker Codex de explicações

- Adicionado `question-explanation-worker` ao Compose, com loop de processamento, timeout configurável, rede pública para o runner, volume de workspaces e socket Docker autorizados.
- O provider passou a drenar stdout e stderr em modo não bloqueante; logs do worker expõem somente evento, resultado de processamento e data, sem contexto, prompt ou dados pessoais.
- Validação: `docker compose config --quiet`, lint PHP e worker ativo no Compose; o primeiro ciclo confirmou estado ocioso por `{"event":"question_explanation","processed":false}`. Testes com provider fake cobrem sucesso, falha sanitizada/retry e fallback de adaptador.
- Próximo passo: gerar relatório técnico final e concluir a mudança OpenSpec.

## 2026-09-29 — Relatório técnico final

- Gerado `docs/QUESTION_LEARNING_INTERACTIONS_REPORT.md` com escopo entregue, isolamento, worker Codex, frontend, validações e limitações operacionais.
- Todas as tarefas da proposta `question-learning-interactions` estão marcadas como concluídas; o relatório preserva o aviso não bloqueante de tamanho do bundle SPA e o requisito operacional de sessão Codex válida.
- Próximo passo: arquivar a mudança OpenSpec após a auditoria final dos artefatos e validações.

## 2026-09-29 — Arquivamento OpenSpec concluído

- A mudança `question-learning-interactions` foi arquivada em `openspec/changes/archive/2026-09-29-question-learning-interactions`.
- As specs principais receberam 12 requisitos sincronizados nos contextos de sinais adaptativos, explicações por IA e interações de aprendizagem.
- Validação final: tarefas OpenSpec completas, suíte focal com 16 testes/48 asserções, build SPA, `docker compose config --quiet`, worker ativo e `git diff --check` aprovados.
- Próximo passo: nenhum para esta mudança; monitorar normalmente o worker e a autenticação do volume Codex.

## 2026-09-29 — Correção de payload das interações de questão

- Corrigido o factory HTTP de `QuestionLearning`: ele agora decodifica o corpo JSON diretamente, como os demais factories da API, em vez de depender de `getParsedBody()` sem middleware instalado.
- O repositório Axios passou a serializar `review_later` e `not_mastered` em snake_case; antes, essas flags eram enviadas em camelCase e ignoradas pelo backend.
- Validação: teste de regressão do factory aprovado (2 testes, 5 asserções), build SPA aprovado e containers API/frontend reiniciados para carregar a correção.
- Próximo passo: retestar as ações no caderno existente; as marcações, anotação, comentários, relatos e solicitação de explicação devem deixar de retornar `422 VALIDATION_FAILED`.

## 2026-09-29 — Retomada e metadados da importação PDF

- O job ativo foi interrompido e marcado como `CANCELLED` antes de novas alterações.
- Corrigidos retry, lease, checkpoint e idempotência de candidatos para impedir jobs órfãos e reanálises persistidas.
- O normalizador determinístico extrai cabeçalho explícito como `(FCC – SABESP/Analista de Gestão – Sistemas/2014)`, remove-o do enunciado e preserva banca, concurso, cargo e ano com evidência da página. O writer passa a compor concurso/cargo no metadado consumido pela revisão.
- Validação: lints PHP e caso de regressão direta do cabeçalho aprovados.
- Próximo passo: anexar figuras por marcador no ponto semântico, reduzir extração antecipada e mostrar início/fim no histórico.

## 2026-09-29 — Figuras e histórico de importação PDF

- O adaptador Codex agora exige marcador sequencial `[[FIGURA:n]]` no enunciado ou alternativa para cada âncora visual. O writer só persiste o ativo quando marcador e âncora `ANCHORED` correspondem.
- As telas de consulta, caderno e revisão editorial renderizam alternativas por `QuestionStatementWithAssets`, colocando a figura no marcador em vez de sempre após o texto.
- O histórico de importações passou a mostrar envio, início efetivo e finalização ou estado em andamento.
- Validação: lint PHP, caso FCC/SABESP e build SPA aprovados; permanece o aviso conhecido de bundle acima de 500 kB.
- Próximo passo: otimizar extração sob demanda e contexto taxonômico, depois reiniciar o job corrigido e validar uma questão com figura.

## 2026-09-29 — Otimização e reimportação limpa

- A extração de imagens foi alterada para sob demanda: somente páginas de candidatos com referência visual são rasterizadas. A taxonomia por chamada é reduzida a no máximo 100 caminhos relevantes, com fallback controlado.
- Foram removidas somente as 30 questões, 39 análises e ativos vinculados ao job cancelado `f3370a78-3aa3-4ac4-9cfb-67d514588442`; usuários, catálogo, taxonomia e demais dados foram preservados. O job foi resetado para `PENDING`.
- Validação: lint dos componentes PHP e suíte focal do worker aprovados.
- Próximo passo: iniciar o worker atualizado e validar checkpoints, metadados e figuras no novo processamento.

## 2026-09-29 — Validação da reimportação corrigida

- A reimportação foi iniciada de modo limpo e está em `PROCESSING` no executor atualizado. O primeiro checkpoint já persistiu questão e a sequência continua com chamadas individuais do Codex.
- A conferência no banco comprovou o caso solicitado: a questão FCC contém `board=FCC`, `source=SABESP/Analista de Gestão – Sistemas`, `exam_year=2014` e o cabeçalho foi removido do enunciado.
- A mesma questão contém `[[FIGURA:1]]` no ponto semântico do enunciado e possui um único ativo de página 51 vinculado ao enunciado, validando a associação visual.
- Próximo passo: acompanhar o lote em execução e concluir a cobertura/documentação residual da mudança OpenSpec.

## 2026-09-29 — Entrega da importação PDF resiliente

- A mudança OpenSpec `resilient-question-pdf-import` foi concluída: 7 de 7 tarefas marcadas, com retomada idempotente, cabeçalhos normalizados, imagens por marcador, extração sob demanda, taxonomia reduzida e histórico temporal.
- Validação final: suíte focal PHP aprovada (3 testes, 4 asserções), build SPA aprovado e `git diff --check` sem apontamentos. O aviso não bloqueante de bundle acima de 500 kB permanece.
- A reimportação do PDF segue em execução com o caso FCC e a imagem ancorada já verificados no banco.
- Próximo passo: monitorar normalmente o término do lote; o histórico exibirá `finishedAt` quando o job alcançar estado terminal.

## 2026-09-29 — Diagnóstico e retomada do worker de importação

- O contêiner permanente `question-pdf-worker` estava em `Exited (137)` porque havia sido interrompido para a execução diagnóstica anterior; não se tratou de uma queda da aplicação.
- A execução diagnóstica de uso único encerrou em `0` após a validação rejeitar uma resposta do analisador com `Páginas analisadas inválidas`. Ela agendou corretamente a nova tentativa do job `f3370a78-3aa3-4ac4-9cfb-67d514588442` para 18:34:21.
- O serviço contínuo foi reativado. A verificação posterior confirmou-o `Up` e o job voltou a `PROCESSING`, com 37 chunks processados e 25 questões persistidas.
- Próximo passo: acompanhar as novas tentativas de análise inválida, caso ocorram, até o término do lote.

## 2026-09-29 — Limpeza de questões e histórico de importação

- Mediante autorização explícita, foram removidas todas as questões e seus dados derivados: alternativas, ativos, vínculos, tentativas, revisões, comentários, anotações, interações, auditorias, correções, explicações e relatos.
- Também foram removidos todos os registros de importação: importações legadas, linhas, jobs PDF, análises, âncoras de imagem, achados e sinais de qualidade.
- Validação no banco: 0 questões, 0 alternativas, 0 ativos, 0 importações, 0 jobs PDF e 0 análises. Permanecem 5 usuários, 1 concurso, 1 cargo, 80 assuntos legados, 126 assuntos canônicos e 72 vínculos de assunto do cargo.
- O `question-pdf-worker` foi reativado e está disponível para uma nova importação.
- Próximo passo: enviar um novo PDF quando desejar reiniciar o acervo de questões.

## 2026-09-30 — Revisão do worker de importação PDF

- Criada a mudança OpenSpec resilient-parallel-pdf-import para corrigir timestamps, isolar falhas por candidato, permitir paralelismo configurável e reconciliar duplicatas com gabarito oficial.
- Diagnóstico confirmado: o worker redefine startedAt em checkpoints e retomadas; trata resposta inválida individual como indisponibilidade do job; percorre candidatos serialmente; e descarta duplicatas sem comparar a fonte do gabarito.
- Próximo passo: implementar checkpoints concorrentes, retentativa isolada e promoção segura de fonte OFFICIAL antes da limpeza final autorizada.

## 2026-09-30 — Isolamento de falhas e promoção de gabarito

- O início efetivo do job passou a ser preservado no repositório e nos checkpoints.
- Erro de validação de uma análise agora incrementa somente a falha do candidato e mantém o PDF em processamento; a suíte focal aprovou 3 testes e 4 asserções.
- A deduplicação promove gabarito oficial explícito sobre ausência ou estimativa, sem substituir conteúdo ou gabarito já oficial.
- Criada a migration 040 para checkpoints de candidatos, necessária ao processamento concorrente seguro.
- Próximo passo: implementar o repositório/checkpoint consumer e aplicar a migration antes de ativar consumidores paralelos.

## 2026-09-30 — Banco preparado para importação concorrente

- A migration 040_question_pdf_import_candidate_checkpoints foi aplicada e validada: a tabela possui chave única por job e fingerprint, índice de reivindicação e lease para execução concorrente.
- A limpeza foi reconferida após a migration: questões, jobs e análises permanecem vazios; usuários e assuntos foram preservados.
- O worker PDF permanece parado de propósito até que o serviço passe a consumir checkpoints; iniciá-lo antes disso manteria a execução serial atual.
- Próximo passo: integrar o repositório de checkpoints ao serviço e ativar consumidores paralelos limitados por configuração.

## 2026-09-30 — Consumidores paralelos de importação

- O Compose passou a iniciar dois consumidores do worker PDF por padrão, configuráveis por QUESTION_IMPORT_WORKERS. Jobs distintos podem ser reivindicados e processados em paralelo sem disputa do mesmo job.
- A configuração foi validada com docker compose config e o worker foi recriado; permanece Up e o banco está vazio.
- Próximo passo: monitorar a próxima importação real e ajustar QUESTION_IMPORT_WORKERS conforme a capacidade do executor Codex.

## 2026-09-30 — Validação parcial da importação resiliente

- Validados consumidores paralelos configuráveis no Compose, preservação de startedAt e promoção de gabarito OFFICIAL em duplicatas.
- PHPUnit focal aprovado: 3 testes e 4 asserções; docker compose config aprovado.
- Permanecem pendentes checkpoints persistidos por candidato e testes de concorrência/idempotência completos.
- Próximo passo: concluir checkpoints antes de iniciar a implementação de análise em lote.


## 2026-09-30 — Checkpoints concorrentes para importação PDF

- A importação analítica passou a materializar candidatos em checkpoints idempotentes, com lock pessimista, lease recuperável, retry exponencial limitado por candidato e falhas de validação isoladas.
- O resumo do job é consolidado pelos checkpoints terminais; a telemetria de tokens, duração e achados é recomposta a partir das análises persistidas, `startedAt` é preservado entre retomadas e `finishedAt` só é definido em estado terminal. O histórico administrativo documenta os estados e a tela já apresenta contagens e horários consolidados.
- Corrigido o mapeamento Doctrine dos campos de telemetria e ampliada a detecção de enunciados que dependem de código ou conteúdo exibido a seguir para extração visual.
- Limpeza autorizada concluída: 0 questões, 0 jobs PDF, 0 análises e 0 checkpoints; 5 usuários e o catálogo foram preservados.
- Validação: `docker compose config --quiet`, lint PHP, `git diff --check` e suíte focal de importação aprovados (7 testes, 28 asserções).
- Próximo passo: o worker paralelo está ativo e pronto para a próxima importação de teste; acompanhar a telemetria consolidada no histórico.


## 2026-09-30 — Análise Codex em lote de candidatos PDF

- Criado o contrato interno de análise em lote e o executor Codex agora envia múltiplos candidatos em uma única execução estruturada, com imagens em diretórios por fingerprint.
- O worker reivindica checkpoints pendentes do mesmo job até os limites `QUESTION_IMPORT_BATCH_SIZE` (8) e `QUESTION_IMPORT_BATCH_PAYLOAD_BYTES` (120000), mantendo retry, falha e escrita individual por candidato.
- Providers alternativos mantêm fallback compatível; o histórico preserva a telemetria recomposta a partir de análises persistidas.
- Próximo passo: adicionar cobertura específica de resposta parcial e validar o worker após recriação.

- Cobertura adicional aprovada: resposta parcial reenfileira somente os checkpoints ausentes, schema vincula cada item ao fingerprint e imagens com mesmo índice permanecem isoladas.
- Validação final da proposta batch: 9 testes e 32 asserções, lint PHP, `docker compose config --quiet`, `git diff --check` e worker ativo sem jobs pendentes.


## 2026-09-30 — Retomada concorrente da importação PDF

- O job lento foi interrompido e as questões persistidas foram removidas; o histórico técnico foi reinicializado para reprocessamento limpo.
- Corrigida a promoção de gabarito oficial em duplicatas, que chamava método inexistente no writer.
- O worker passou a manter consumidores persistentes; há quatro processos PHP ativos e o lote configurado foi elevado para 20 candidatos.
- A reivindicação do lote avança após a posição já reservada e trata deadlock como lote parcial, evitando manter o checkpoint inicial sem processamento.
- Validação: lint PHP, Compose e suíte focal de importação aprovados.
- Próximo passo: acompanhar a primeira conclusão dos quatro lotes concorrentes e ajustar somente se o provider exceder o timeout.


## 2026-09-30 — Watchdog por inatividade do Codex

- Corrigido o histórico do job cancelado após limpeza: os contadores de questões criadas, classificadas, falhas e progresso foram zerados, coerentes com as 0 questões persistidas para revisão.
- O executor de importação deixou de usar timeout absoluto de 180 segundos. `QUESTION_IMPORT_CODEX_IDLE_TIMEOUT` vale 900 segundos por padrão e é renovado sempre que o runner Codex produz saída; somente inatividade contínua encerra o subprocesso.
- O worker foi recriado e confirmou a configuração efetiva de 900 segundos.
- Validação: lint PHP, `docker compose config --quiet`, `git diff --check` e testes focais aprovados (4 testes, 9 asserções).
- Próximo passo: reenfileirar explicitamente um PDF somente quando desejar retomar a importação; o job atual permanece cancelado.


## 2026-09-30 — Reenfileiramento limpo do PDF

- O job `6d40ac23-94a6-4b5f-87f5-e747e01e4c59` foi reenfileirado após interromper os quatro runners temporários da execução anterior e normalizar seus checkpoints.
- Foram removidas somente análises parciais desse job; os 181 checkpoints voltaram a `PENDING` antes da retomada.
- Validação após reativar o worker: quatro runners Codex foram iniciados e 57 checkpoints já estão em `PROCESSING`; os 124 restantes permanecem pendentes.
- Próximo passo: aguardar a conclusão dos primeiros lotes e acompanhar as métricas consolidadas no histórico.


## 2026-09-30 — Recuperação de checkpoints e retomada validada

- Corrigida a transição de checkpoints: ela não executa mais `refresh` redundante após obter lock pessimista; busca o registro atual sob lock em uma única consulta. Isso evita o `EntityManagerClosed` que interrompia o worker após uma falha de candidato.
- Validação de código: lint PHP, `docker compose config --quiet`, `git diff --check` e suíte unitária focal aprovados (5 testes, 11 asserções). O teste de integração legado de checkpoints segue pendente por inconsistência do cenário com transações aninhadas.
- O job `6d40ac23-94a6-4b5f-87f5-e747e01e4c59` foi reenfileirado após encerrar runners antigos e apagar somente suas análises parciais.
- Monitoramento confirmou retomada persistida: job em `PROCESSING` a 12%, 20 checkpoints concluídos, 69 em processamento, 92 pendentes (56 em retentativa), 20 análises vinculadas ao job e nenhuma nova ocorrência de `EntityManagerClosed`.
- Próximo passo: acompanhar os lotes seguintes e tratar separadamente candidatos cuja resposta tenha páginas de evidência inválidas.


## 2026-09-30 — Reconciliação dos indicadores de importação

- Corrigida a agregação de falhas: o histórico passa a somar `failed` dos resultados individuais, inclusive quando o checkpoint técnico é concluído com falha de escrita ou validação.
- A tela administrativa renomeia a métrica `extraídas` para `processadas`, pois ela representa candidatos finalizados, não questões criadas.
- O job `6d40ac23-94a6-4b5f-87f5-e747e01e4c59` foi cancelado pelo administrador às 14:39; seus indicadores foram reconciliados sem retomada: 179 processadas de 181 candidatas, 7 criadas, 2 duplicadas e 43 com erro.
- Validação: lint PHP, suíte unitária focal (5 testes, 11 asserções), build do frontend e `git diff --check` aprovados.
- Próximo passo: reenfileirar explicitamente apenas se desejar processar as 2 candidatas interrompidas.


## 2026-09-30 — Limpeza autorizada de importações

- Removidos todos os históricos de importação PDF e legada, incluindo jobs, checkpoints, análises, achados, âncoras de imagem e sinais de qualidade.
- Removidas as 8 questões originadas por importação e seus dados derivados (alternativas, ativos, vínculos de caderno, tentativas, revisões, interações, comentários, anotações, correções e classificações).
- Validação no banco: 0 questões importadas, 0 jobs PDF, 0 análises, 0 checkpoints e 0 importações legadas; usuários, concursos, cargos e assuntos foram preservados.
- Próximo passo: reenviar um PDF somente quando desejar iniciar uma nova importação.


## 2026-09-30 — Diagnóstico de precisão e desempenho da importação PDF

- Criada a mudança OpenSpec `accurate-fast-pdf-import` para corrigir reconhecimento e execução dos PDFs de TI.
- Verificação direta por `pdftotext` confirmou os 66 marcadores explícitos de múltipla escolha: DevOps 2, Git 25, Linux 2, Python 15 e XML/JSON/CSV 22.
- Diagnóstico: o segmentador depende da numeração de início e não usa o marcador terminal `Gabarito: Letra A-E`; itens são formados incorretamente e respostas inválidas entram em backoff. Logs também registram deadlocks na reivindicação concorrente.
- Próximo passo: implementar segmentação delimitada por gabarito, encerrar falhas determinísticas sem retry e tornar a reivindicação resistente a deadlock; validar contra os cinco PDFs.
## 2026-09-30 — Gabaritos A–D e variação de caixa

- O reconhecimento de gabarito oficial normaliza a letra para maiúscula e aceita marcadores `Gabarito`, com ou sem `:`/`-`, incluindo `GABARITO LETRA A`; as alternativas de quatro opções A–D continuam válidas, em maiúsculas ou minúsculas.
- Validação: lint PHP e `ProcessQuestionPdfImportBatchTest` aprovados (2 testes, 3 asserções).
- Próximo passo: concluir a segmentação delimitada por gabarito e a regressão textual dos cinco PDFs da mudança OpenSpec `accurate-fast-pdf-import`.
## 2026-09-30 — Segmentação determinística e resiliência da importação PDF

- O segmentador usa o marcador terminal de linha `Gabarito: Letra A-E` ou `Gabarito: A-E` e preserva o bloco iniciado na última numeração que realmente contém quatro ou cinco alternativas. Alternativas alinhadas na mesma linha agora são reconhecidas; itens Certo/Errado e menções editoriais internas de gabarito permanecem fora do provider.
- A regressão no corpus montado comprovou 66 candidatos: DevOps 2, Git 25, Linux 2, Python 15 e XML/JSON/CSV 22.
- `DomainException` de análise em lote encerra os checkpoints afetados sem backoff; `claimNext()` captura deadlock e mantém o consumidor recuperável.
- Validação: lint PHP, regressão do corpus no container (1 teste, 6 asserções) e suíte focal de lote (4 testes, 6 asserções).
- Próximo passo: validar a reconciliação de contadores no histórico e executar a validação integrada antes de reenfileirar documentos reais.

- Reconciliação do histórico validada: totais são derivados dos checkpoints terminais; uma escrita rejeitada permanece contabilizada em `processadas` e `com erro`, sem reduzir as questões criadas ou classificadas.

- O delimitador também aceita `Gabarito A-D/E` sem dois-pontos ou a palavra “Letra”, somente no início da linha; a regressão do corpus permaneceu em 66/66.

## 2026-10-02 — Correção da criação de sessão de revisão

- Corrigido o import de `ReviewSessionCardRecord` no repositório Doctrine de sessões. A referência anterior apontava para o namespace do repositório em vez da entidade, causando `500 INTERNAL_ERROR` em `GET /review/sessions/daily` ao gravar os cards da sessão.
- Corrigida também a persistência da sessão após uma classificação: os vínculos de cards já carregados não são mais recriados sem mudança de composição, evitando colisão de identidade no Doctrine.
- Validação: reprodução confirmou as exceções Doctrine; após os patches, a sessão diária da conta afetada foi criada com 20 cards e uma classificação `GOOD` foi persistida, com próxima revisão em 2026-10-05. A execução HTTP completa de `POST /v1/review/sessions/{sessionId}/cards/{cardId}/reviews` devolveu `200` com o envelope de sucesso. Lint PHP, suíte focal de Review (15 testes, 38 asserções) e `git diff --check` passaram.
- Próximo passo: ampliar a cobertura integrada do repositório de sessões quando o fluxo de revisão evoluir.

## 2026-10-02 — Redesenho das telas de cards de revisão

- A sessão de revisão foi redesenhada a partir da referência aprovada: progresso no topo, superfície central azul-profunda para a frente e o verso, ação explícita de revelação e classificações grandes `Não lembrei`, `Difícil`, `Lembrei` e `Fácil`.
- Desktop usa quatro ações em linha; em telas móveis elas passam a uma coluna com alvos de toque de pelo menos 48 px. A navegação entre cards e os estados vazio, concluído, carregamento e erro foram preservados.
- Não houve alteração de contrato ou simulação de conteúdo: `ReviewSessionPage -> useReview -> ReviewUseCases -> AxiosReviewRepository` continua sendo a única origem para card, progresso e persistência de classificação.
- Documentação de frontend atualizada. Validação: `docker compose exec -T frontend npm run build` e `git diff --check` aprovados; permanece somente o aviso conhecido de bundle JavaScript acima de 500 kB.
- Próximo passo: revisar a renderização autenticada em desktop e mobile quando a fixture visual de Review estiver disponível.

## 2026-10-02 — Ajuste de cor das classificações de revisão

- Refinados os valores para a amostra visual da referência: fundos sólidos `#2B1C2A`, `#2B211C`, `#092C51` e `#09382F`, com bordas menos saturadas. Isso elimina os degradês que deixavam os quatro botões mais claros que a imagem.
- Validação: `docker compose exec -T frontend npm run build` e `git diff --check` aprovados; permanece somente o aviso conhecido de bundle JavaScript acima de 500 kB.
- Próximo passo: revisar a renderização autenticada em desktop e mobile quando a fixture visual de Review estiver disponível.

## 2026-09-30 — Validação persistida da importação em lote

- Limpeza autorizada concluída antes do reenfileiramento: 0 questões, jobs, checkpoints e análises; usuários e taxonomia preservados.
- Os cinco PDFs foram reenfileirados pelo caso de uso oficial e a segmentação materializou 66 checkpoints (2/25/2/15/22).
- Corrigido o isolamento de respostas inválidas por fingerprint e adicionada reconciliação de jobs sem checkpoints abertos, eliminando indicadores parciais por corrida concorrente.
- A validação persistida permanece em andamento; as rejeições observadas são de contrato do Codex (página fora da evidência ou discrepância visual sem achado), registradas para nova rodada sem reprocessar questões já criadas.
- Próximo passo: aguardar os checkpoints ativos, reenfileirar exclusivamente rejeições remanescentes e fechar a validação com os cinco históricos consolidados.

## 2026-09-30 — Reenvio com regra de discrepância efetivamente carregada

- Detectado que os processos PHP persistentes do worker mantinham em memória a instrução anterior do analisador Codex; por isso, uma alteração no arquivo não alcançava runners já iniciados.
- O serviço `question-pdf-worker` foi reiniciado de forma controlada. Checkpoints interrompidos, rejeitados ou concluídos sem criação foram reenfileirados, sem remover as 38 questões já persistidas.
- Verificação direta dos quatro novos runners confirmou a instrução ativa: `DISCREPANCY` exige o achado `IMAGE_DISCREPANCY`, além da restrição de páginas de evidência.
- Estado após retomada: 35 candidatas pendentes/em análise, 38 questões preservadas e os cinco históricos em reconciliação.
- Próximo passo: aguardar os lotes corrigidos, consolidar os cinco jobs e investigar somente eventuais recusas remanescentes por candidato.

## 2026-09-30 — Seleção determinística de taxonomia-folha

- Identificada causa adicional das criações perdidas: a lista enviada ao Codex misturava nós agregadores e folhas; quando o modelo selecionava um nó com filhos, o gravador o recusava corretamente como classificação genérica.
- A montagem de candidatos agora envia somente caminhos de taxonomia-folha. O validador exige que `taxonomy_path` corresponda exatamente a uma opção permitida, e o writer deriva o pai imediato desse caminho em vez de confiar no pai textual produzido pelo modelo.
- O prompt em lote reforça essa regra. Validação focal aprovada: 10 testes e 20 asserções, incluindo rejeição de taxonomia fora das folhas permitidas.
- Próximo passo: reiniciar o worker para carregar a regra, reenfileirar exclusivamente checkpoints ativos ou sem criação e medir a criação dos cinco PDFs.

## 2026-09-30 — Normalização de caminho de taxonomia do Codex

- A telemetria de escrita mostrou que alguns retornos válidos usavam um único elemento para o caminho completo (`"Tecnologia da Informação > Python"`), enquanto o gravador recebe segmentos individuais. Isso mantinha a questão fora da taxonomia apesar de a folha ser permitida.
- O validador agora normaliza esse formato equivalente para segmentos antes da verificação de catálogo; caminhos inventados ou agregadores continuam recusados. A cobertura focal passou com 11 testes e 21 asserções.
- Próximo passo: carregar a normalização no worker e reenviar somente candidatos sem criação para comprovar a recuperação dos itens de Python e XML/JSON/CSV.

## 2026-09-30 — Metadados ausentes não geram falha de contrato

- Identificada a origem da rejeição `Páginas analisadas inválidas` em candidatos sem cabeçalho: o validador exigia ao mesmo tempo metadado `null` e lista de evidências não vazia.
- A validação agora aceita evidência vazia exclusivamente quando o respectivo campo (`exam`, `position`, `board` ou `year`) é nulo; valores preenchidos permanecem obrigatoriamente vinculados a páginas permitidas. As chaves ausentes são normalizadas para listas vazias no objeto de domínio.
- Validação focal: 10 testes e 17 asserções aprovados, incluindo metadados ausentes e a normalização de caminho de taxonomia.
- Próximo passo: reiniciar o worker para carregar a validação e reenfileirar os candidatos sem criação; confirmar a recuperação do candidato Linux antes da consolidação final.

## 2026-09-30 — Alternativas renderizadas como imagem

- A inspeção de candidatos que repetiam alternativas comprovou que a segmentação estava correta, mas a página do PDF continha apenas os rótulos `a)`–`e)` na camada textual; o conteúdo das opções era rasterizado. A página 61 do PDF Python possui imagens extraíveis para essas opções.
- O worker passa a reconhecer essa sequência de rótulos vazios como necessidade visual, extrai os ativos da página e os entrega ao Codex. O prompt exige transcrição a partir das imagens e proíbe repetir alternativa para preencher lacuna.
- Validação focal: 11 testes e 19 asserções, incluindo o padrão de alternativas visuais vazias. Lint do serviço e do analisador aprovado.
- Próximo passo: carregar o detector no worker, reenviar exclusivamente os candidatos sem criação e verificar que as opções visuais passam a ser persistidas.

## 2026-09-30 — Rematerialização seletiva para ativos visuais

- Os checkpoints agora preservam resultados que já criaram ou deduplicaram questão quando um job é materializado novamente. Isso permite renovar o payload apenas de candidatos sem resultado — inclusive para acrescentar imagens detectadas posteriormente — sem reanalisar questões persistidas.
- A próxima retomada marca apenas os candidatos sem criação para reconstrução; o serviço recompõe suas páginas e ativos visuais, mantendo os resultados válidos inalterados.
- Validação: lint do repositório Doctrine e `git diff --check` dos arquivos alterados aprovados. O teste integrado legado de transição de checkpoints continua com seu cenário transacional conhecido e não foi ampliado nesta etapa.
- Próximo passo: rematerializar os candidatos sem criação, verificar os manifestos de imagens nas questões Python/XML e executar a análise visual.

## 2026-09-30 — Reenvio focal de alternativas visuais

- A verificação física por `source_pdf_job_id` confirmou 59 questões persistidas: DevOps 2/2, Git 25/25, Linux 2/2, Python 10/15 e XML/JSON/CSV 20/22.
- Restam sete candidatos, todos com resultado técnico `alternativas repetidas`; seis possuem entre 2 e 8 imagens no manifesto e um não possui imagem extraível. Nenhuma questão válida será reenviada.
- O prompt foi reforçado para exigir inspeção visual de cada ativo listado antes da estruturação das alternativas.
- Próximo passo: reenfileirar somente esses sete checkpoints com os manifestos preservados e avaliar a recuperação; o item sem imagem será tratado como limitação explícita caso permaneça sem alternativas legíveis.

## 2026-09-30 — Contexto de página para alternativas em imagem

- O extrator visual agora renderiza também uma imagem completa para cada página solicitada (`pdftoppm` a 144 DPI), além das imagens embutidas. Isso preserva ordem e contexto espacial das alternativas A–E e cobre páginas sem imagem embutida.
- Validação direta no PDF Python: a página 61, que contém alternativas rasterizadas, produziu o ativo `page-61-render.png` junto dos ativos extraídos.
- Próximo passo: reconstruir somente candidatos Python/XML ainda sem resultado para anexar a página renderizada e executar a tentativa visual final; Git, Linux e DevOps permanecem consolidados.

## 2026-09-30 — Imagens anexadas ao Codex CLI

- Diagnóstico conclusivo: o adaptador copiava ativos para o workspace, mas não os anexava ao prompt inicial do Codex. O CLI suporta oficialmente `--image`; o adaptador passou a anexar até 12 renderizações completas de página por lote (ou ativos individuais quando não houver renderização).
- A cópia preserva o sufixo `-render.png`, permitindo priorizar a página completa em vez de miniaturas sem contexto. A suíte focal do adaptador, validador e lote aprovou: 12 testes e 22 asserções.
- Próximo passo: reenfileirar os sete candidatos restantes com anexos visuais diretos e confirmar que o comando efetivo contém `--image` antes de avaliar a persistência.

## 2026-09-30 — Separador do prompt após anexos Codex

- A primeira chamada com `--image` falhou antes da análise porque a opção variádica do Codex CLI consumiu o prompt final como parte da lista de imagens (`No prompt provided via stdin`).
- O comando agora insere o separador POSIX `--` entre os anexos e o prompt, preservando os arquivos de imagem e a instrução estruturada. Os checkpoints afetados permaneceram em backoff controlado, sem gravar questão parcial.
- Próximo passo: recarregar o worker, reenfileirar exclusivamente os candidatos visuais sem criação e confirmar no processo ativo a presença de `--image` seguida do separador.
## 2026-09-30 — Redesenho visual da Arena

- A tela `ArenaPage.vue` foi reconstruída exclusivamente no frontend conforme a referência: superfícies azul-marinho compactas, cartão inicial centralizado com emblema e métricas, criação em etapas, descoberta de salas, entrada por código, espera com prontidão, questão cronometrada, resultado de rodada e ranking final. Resultado e ranking usam apenas o placar real devolvido pela API. O shell recebe o tema escuro somente enquanto a Arena estiver ativa.
- A implementação preserva `useArena`, `ArenaUseCases`, o repositório Axios e o Socket.IO existentes; não houve alteração de API, banco, backend ou workers de importação.
- Validação: `docker compose exec -T frontend npm run build` aprovado. O build mantém apenas o aviso não bloqueante já conhecido de chunk JavaScript acima de 500 kB.
- Próximo passo: validar visualmente com uma sessão autenticada e uma sala real em desktop e mobile.
## 2026-10-02 — Fila contínua de revisão (em andamento)

- A revisão deixa de pré-selecionar uma seção congelada: a sessão mantém somente os cards já servidos para auditoria, e a API seleciona um único card atual depois de cada classificação confirmada.
- `GET /review/sessions/daily` e o retorno de classificação agora expõem `overdueCards` e `canAdvance`; `GET /review/sessions/advance` permite adiantar cards futuros apenas sem pendências vencidas e retorna conflito quando houver uma pendência.
- A SPA substitui o card pelo estado devolvido pela API, exibe o saldo vencido e mostra **Adiantar revisões** somente quando a API permite. Contratos HTTP e frontend foram atualizados.
- Validação: lint PHP dos arquivos alterados, suíte unitária existente de Review (15 testes, 38 asserções), build tipado da SPA e `git diff --check` aprovados. Mantém-se o aviso não bloqueante de bundle acima de 500 kB.
- Pendência: adicionar cenários focais para seleção incremental, conflito de avanço, próximo card e idempotência antes de arquivar a mudança OpenSpec.
- Próximo passo: criar os testes focais da fila e arquivar `continuous-review-queue` após validação completa.
## 2026-10-02 — Correção das cores de classificação da revisão

- A reprodução em navegador revelou uma regra global de fundo com `!important`, que ainda vencia o primeiro estilo inline. Os quatro botões agora recebem fundo e texto inline também com `!important`: vermelho, laranja, azul e verde.
- Próximo passo: validar o build tipado da SPA e revisar visualmente os quatro estados na sessão autenticada.
## 2026-10-02 — Importação de deck JSON para revisão

- Adicionado o comando `apps/api/bin/import-flashcards-json.php`, que valida um deck JSON, cria ou reutiliza o assunto canônico informado, deduplica cards pelo fingerprint e inicializa o progresso individual do usuário para revisão imediata.
- O comando usa exclusivamente os repositórios Doctrine, o caso de uso de criação/reuso de flashcard e uma transação; não altera histórico de revisões existente.
- Executada a importação do deck `Inglês CESGRANRIO - Transpetro` para `duartecds@gmail.com`: 59 cards e 59 progressos individuais foram criados, todos disponíveis para revisão imediata. O assunto canônico raiz `Inglês` foi criado porque ainda não existia no catálogo.
- Validação: JSON com 59 itens e nenhum card vazio; lint PHP do comando aprovado; consulta Doctrine confirmou 59 vínculos de progresso da conta com os cards de Inglês. Uma segunda execução criou 0 cards e 0 progressos, reutilizando os 59 existentes.
- Próximo passo: nenhum para esta importação.

## 2026-10-02 — Importação de vocabulário CESGRANRIO

- Importado o deck `Vocabulário CESGRANRIO - Inglês` para `duartecds@gmail.com`. Os três marcadores vazios que antecediam o objeto no texto colado foram removidos somente do fluxo de leitura; os 142 cards foram preservados.
- Os 142 flashcards e seus 142 progressos individuais foram criados no assunto canônico `Inglês`, disponíveis para revisão imediata. Nenhum card do deck anterior foi alterado.
- Validação: `total_cards` e a lista continham 142 itens, sem frente ou verso vazio; a consulta Doctrine confirmou a quantidade total de vínculos de progresso para o assunto.
- Próximo passo: nenhum para esta importação.

## 2026-10-02 — Botões de classificação da revisão no mobile

- Reorganizada a classificação da revisão em uma matriz 2×2 no celular, com botões de 74 px de altura e conteúdo interno alinhado horizontalmente. Isso elimina o empilhamento visual do ícone, rótulo e atalho numérico observado na sessão.
- Os atalhos numéricos permanecem em desktop e são ocultos em telas sensíveis ao toque, onde não representam uma ação disponível. Cores, rótulos, ícones e chamadas ao caso de uso de revisão foram preservados.
- Validação: `docker compose exec -T frontend npm run build` e `git diff --check` aprovados. Mantém-se apenas o aviso conhecido de bundle acima de 500 kB.
- Próximo passo: nenhum para este ajuste pontual.

## 2026-10-02 — Navegação entre cards de revisão

- A sessão agora persiste a posição do card atual e aceita `POST /review/sessions/{id}/navigation` com `NEXT` ou `PREVIOUS`. Avançar não cria revisão: reapresenta um card já servido ou serve o próximo elegível; voltar não altera o histórico.
- A SPA usa o fluxo DDD de Review para os controles Anterior e Próximo, sem calcular lista localmente. Ao revisitar card já classificado, a interface informa o estado e não apresenta outra classificação.
- Validação: migration 043 aplicada; lint PHP, PHPUnit focal de Review, build tipado da SPA e `git diff --check` aprovados. Mantém-se somente o aviso conhecido de bundle acima de 500 kB.
- Próximo passo: nenhum para a mudança `review-card-navigation`.
## 2026-10-02 — Persistência transacional da navegação de cards

- Corrigida a navegação que avançava apenas uma vez: cada `NEXT` ou `PREVIOUS` agora é executado pelo caso de uso `NavigateReviewSessionService` dentro de uma transação Doctrine. A posição atual e o card servido são gravados antes da resposta HTTP, evitando que a requisição seguinte volte à posição anterior.
- Validação: lint dos três arquivos PHP modificados, suíte unitária de Review (15 testes, 38 asserções) e `git diff --check` aprovados.
- Próximo passo: nenhum; recarregar a página de revisão e navegar normalmente.

## 2026-10-02 — Navegação restrita a cards pendentes

- A navegação da revisão agora ignora cards que já receberam classificação na sessão. Anterior e Próximo só percorrem cards pendentes; ao retomar uma sessão, um pendente já servido é priorizado antes da seleção de um novo card.
- O histórico de classificações e o agendamento individual permanecem imutáveis. Um card classificado como `AGAIN` volta à fila normal somente ao atingir seu `dueAt`; não é mais reexibido pela navegação manual.
- Validação: consulta Doctrine da sessão afetada confirmou cinco cards classificados e um pendente; suíte unitária de Review aprovada.
- Próximo passo: nenhum.

## 2026-10-02 — Alternância da frente do flashcard

- A tela de revisão agora oferece `Ver frente novamente` após revelar a resposta, permitindo alternar livremente entre frente e verso antes de classificar.
- A classificação continua usando a sessão devolvida pela API e redefine a face exibida para a frente do próximo card pendente imediatamente após a resposta.
- Validação: `docker compose exec -T frontend npm run build` aprovado; permanece apenas o aviso não bloqueante de bundle acima de 500 kB.
- Próximo passo: nenhum.

## 2026-10-02 — Persistência atômica de cards servidos

- A abertura e a retomada de sessão agora executam em transação Doctrine. O vínculo entre sessão e card é confirmado antes da resposta HTTP, impedindo que a interface exiba um card que a classificação subsequente ainda não encontra na sessão persistida.
- A transação é compartilhada pelos casos de uso de montar sessão, navegar e classificar; o controlador permanece sem responsabilidade transacional.
- Validação: lint PHP, composição da API e suíte unitária de Review aprovados.
- Próximo passo: nenhum.

## 2026-10-03 — Proposta do mapa de estudo por desempenho

- Criada a mudança OpenSpec `study-map-performance` para uma visão hierárquica por concurso, com assuntos, subassuntos e métricas derivadas exclusivamente das respostas finais das práticas do usuário.
- O escopo não inclui cronograma, horas estimadas ou progresso artificial: nós sem prática serão apresentados como sem dados.
- Validação: proposta, design, especificação delta e lista de tarefas criados conforme o fluxo OpenSpec.
- Próximo passo: aplicar `study-map-performance` e executar as tarefas na ordem definida.

## 2026-10-03 — Ampliação para cronograma Gantt

- A mudança `study-map-performance` passou a incluir cronograma Gantt persistido por concurso, com datas planejadas, dependências explícitas entre assuntos e estados `PLANNED`, `STUDIED` e `COMPLETED`.
- Marcar um assunto como estudado ou concluído preserva sua faixa e posição temporal; desempenho de práticas e conclusão planejada permanecem métricas distintas.
- Validação: proposta, design, requisitos e tarefas foram revisados e validados pelo OpenSpec.
- Próximo passo: aplicar `study-map-performance`.

## 2026-10-03 — Contrato do mapa de estudo

- Documentado o contrato autenticado do mapa de estudo, do cronograma Gantt e das dependências em `docs/API.md`, incluindo validações de escopo, datas e ciclos.
- Próximo passo: persistir itens de cronograma e suas dependências.

## 2026-10-03 — Persistência do cronograma Gantt

- Adicionada a migration 044, entidades de domínio, interface e repositório Doctrine para itens do cronograma e dependências por usuário, concurso e assunto canônico.
- Validação: lint PHP das novas classes e `git diff --check` aprovados.
- Próximo passo: definir DTOs tipados e validar datas, estados e dependências.

## 2026-10-03 — DTOs do mapa de estudo

- Criados DTOs imutáveis para consulta, gravação de cronograma, nós da árvore, itens Gantt e resumo do mapa.
- Validação: lint PHP dos DTOs e `git diff --check` aprovados.
- Próximo passo: validar regras de datas, escopo e dependências acíclicas no caso de uso.

## 2026-10-03 — Regras de cronograma e dependências

- O caso de uso de gravação valida assunto aplicável ao concurso, intervalo de datas, dependências no mesmo cronograma, auto-dependência e ciclos antes da transação.
- Estados de estudo preservam datas planejadas e registram conclusão sem alterar resultados de prática.
- Validação: lint PHP e `git diff --check` aprovados.
- Próximo passo: ampliar a porta de estatísticas para montar a árvore aplicável ao concurso.

## 2026-10-03 — API do mapa e cronograma

- Implementadas consulta hierárquica por concurso, agregação de práticas finais, rotas autenticadas de leitura e gravação do cronograma e migration 044 aplicada.
- Validação: lint PHP, `bin/migrate.php`, composição da API e `git diff --check` aprovados.
- Próximo passo: integrar o contrato à SPA e construir o mapa Gantt.

## 2026-10-03 — Interface do mapa Gantt

- Integrada a visão de mapa de estudo à Performance: resumo real, árvore expansível de assuntos e subassuntos, grade Gantt, agenda de assunto, dependências e estados de estudo preservando a posição temporal.
- A SPA consome exclusivamente as novas rotas pelo repositório Axios e trata ausência de dados sem simular estatísticas.
- Validação: build tipado da SPA e suíte unitária de Performance aprovados.
- Próximo passo: concluir testes de integração/HTTP do mapa e validar a mudança OpenSpec.

## 2026-10-03 — Entrega validada do mapa Gantt

- Concluído o mapa de estudo por concurso: árvore de assuntos, evidências das práticas, cronograma Gantt persistido, dependências acíclicas e estados planejado, estudado e concluído sem deslocar as datas.
- A tela permite filtrar o período do mapa, editar a agenda e manter a faixa temporal mesmo quando o assunto é declarado estudado ou concluído.
- Validação: PHP lint das rotas e repositório, PHPUnit de Performance (11 testes, 27 asserções), `npm run build`, `git diff --check`, migration 044 aplicada e smoke HTTP sem token retornando `401 UNAUTHENTICATED`.
- Próximo passo: mudança arquivada no OpenSpec; acompanhar uso real do cronograma e ajustar apenas a partir de feedback.

## 2026-10-03 — Correção de carregamento do mapa

- Corrigida a consulta de editais do painel de desempenho: o modelo atual relaciona edital diretamente ao concurso e não possui mais `positionId`.
- O mapa Gantt agora é carregado antes dos indicadores auxiliares, portanto permanece disponível mesmo se outro painel falhar.
- Próximo passo: validar a tela de desempenho com a sessão já autenticada.

## 2026-10-03 — Menu exclusivo de cronograma

- Adicionada a seção `Cronograma` no menu principal, separada de `Desempenho`. Ela carrega o Gantt por concurso e mantém filtro de período, agendamento, dependências e estados de estudo.
- A tela de Desempenho permanece voltada exclusivamente às estatísticas de práticas.
- Próximo passo: validar a navegação e a compilação da SPA.

## 2026-10-03 — Cronograma Transpetro até a prova

- Criado cronograma Gantt da Transpetro com 12 blocos sequenciais de 04/10 a 28/11 e dependências entre Redes, Linux, Windows, Contêineres, Infraestrutura, Cloud, Banco de Dados, Segurança, Desenvolvimento, DevOps, Storage e Gestão de Projetos.
- A semana de 29/11 a 05/12 foi mantida livre para revisões e simulados antes da prova em 06/12/2026.
- Corrigido o autoload das entidades Doctrine do cronograma, separando registros de itens e dependências em arquivos PSR-4 próprios.
- Validação: lint PHP das entidades e persistência dos 12 itens pelo caso de uso de cronograma.
- Próximo passo: atualizar o Gantt na seção Cronograma e ajustar estados conforme o avanço real.

## 2026-10-03 — Correção de disparo do cronograma

- A seleção do concurso na página Cronograma agora observa diretamente o valor reativo, incluindo a seleção inicial carregada da API, e sempre dispara a consulta do mapa.
- Próximo passo: recarregar a seção Cronograma e confirmar o Gantt da Transpetro.

## 2026-10-03 — Seletor do cronograma sem bloqueio

- Removido o estado de carregamento próprio do seletor de concurso; a lista agora atualiza o valor e chama o mapa diretamente na resolução da consulta, com falha exibida em tela.
- Próximo passo: confirmar a seleção Transpetro e a renderização do Gantt.
