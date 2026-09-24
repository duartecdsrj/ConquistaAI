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
