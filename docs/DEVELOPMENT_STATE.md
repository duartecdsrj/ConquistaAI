# Estado de desenvolvimento — ConquistaAI

Atualizado em 24/09/2026. Este é o registro de handoff obrigatório antes de iniciar uma nova etapa. Ele complementa o cronograma e reduz a dependência do histórico de conversa.


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
