# Estado de desenvolvimento — ConquistaAI

Atualizado em 24/09/2026. Este é o registro de handoff obrigatório antes de iniciar uma nova etapa. Ele complementa o cronograma e reduz a dependência do histórico de conversa.

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
- A migration `013_assistant_rag.sql` foi aplicada e confirmada no MySQL local. Validações: PHPUnit (40 testes, 102 assertions), build frontend e `git diff --check` aprovados; a rota sem credencial retorna 401, comprovando o registro e a proteção HTTP.
- Próxima etapa autorizada: M7 — descoberta web por providers permitidos.
