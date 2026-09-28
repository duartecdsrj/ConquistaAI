# Estado de desenvolvimento — ConquistaAI

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

Atualizado em 27/09/2026. Este é o registro de handoff obrigatório antes de iniciar uma nova etapa. Ele complementa o cronograma e reduz a dependência do histórico de conversa.

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
