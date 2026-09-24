# Estado de desenvolvimento — ConquistaAI

Atualizado em 23/09/2026. Este é o registro de handoff obrigatório antes de iniciar uma nova etapa. Ele complementa o cronograma e reduz a dependência do histórico de conversa.

## Como retomar em outro ambiente
> **Regra obrigatória para IAs e contribuidores:** sempre que houver avanço de desenvolvimento — criação, alteração, validação, commit, descoberta de bloqueio ou mudança de próxima etapa — atualize este arquivo no mesmo ciclo, antes de iniciar outra funcionalidade. Não dependa somente do histórico da conversa ou de commits para transmitir contexto.

1. Leia `AGENTS.md`, `docs/ARCHITECTURE.md`, `docs/API.md` e este arquivo.
2. Execute `git status --short` e preserve qualquer alteração pendente.
3. Leia a seção **Próxima etapa autorizada** antes de alterar código.
4. Ao concluir um incremento, atualize este arquivo, a documentação de contrato aplicável e registre as validações executadas.

## Marco ativo

**M0 — Taxonomia canônica**, ainda em andamento.

A meta do marco é entregar árvore global de assuntos, aliases, prevenção de ciclos, revisão de duplicidade e auditoria de fusões. Consulte `docs/DEVELOPMENT_SCHEDULE.md` para os critérios completos.

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
