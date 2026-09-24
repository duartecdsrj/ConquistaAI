# Cronograma de desenvolvimento — ConquistaAI

Atualizado em 23/09/2026. Cada marco só avança quando backend, frontend, documentação e testes proporcionais estiverem concluídos.

| Marco | Entrega integrada | Dependências | Critério de aceite | Situação |
|---|---|---|---|---|
| M0 | Taxonomia canônica: árvore, aliases, prevenção de ciclos e revisão de duplicidade | Nenhuma | assunto global, aliases e auditoria funcionais | Em andamento |
| M1 | Concursos, cargos e editais estruturados | M0 | PDF preservado, hash idempotente e conteúdo por cargo | Planejado |
| M2 | Jobs e processamento de edital | M1 | extração assíncrona com progresso e proveniência | Planejado |
| M3 | Questões reais, fontes e deduplicação | M0, M1 | importação idempotente e revisão de candidatos | Planejado |
| M4 | Estatísticas hierárquicas e dashboard por edital | M0, M3 | agregação por descendentes e dados insuficientes | Planejado |
| M5 | Cadernos inteligentes e plano de estudos | M4 | seleção e recomendação explicáveis | Planejado |
| M6 | IA auditável e RAG de editais | M1, M2, M5 | IA desacoplada, evidenciada e revisável | Planejado |
| M7 | Descoberta web por providers permitidos | M1, M3 | proveniência, limites de acesso e jobs | Planejado |

## Cadência por marco

1. Contrato e migration.
2. Domínio, repositórios, serviços e testes.
3. API e autorização.
4. Módulo Axios, casos de uso, composable e tela Quasar.
5. Documentação, validação de build e testes.
6. Revisão do critério de aceite antes do próximo marco.

## M0 — sequência detalhada

- M0.1: tabelas canônicas, aliases e auditoria de fusão. **Concluída nesta entrega inicial.**
- M0.2: domínio Taxonomy, slug/normalização, operações de árvore e prevenção de ciclo. **Em andamento: normalização, prevenção de ciclo e criação persistida concluídas.**
- M0.3: API administrativa, testes e tela Quasar. **Em andamento: criação, listagem paginada e árvore administrativa conectadas; aliases concluídos; edição e movimentação de nós sem filhos conectadas; atualização recursiva de subárvores pendente.**
- M0.4: árvore de assuntos, aliases, mover e editar no frontend.
- M0.5: sugestões de duplicidade, proposta de fusão e reatribuição transacional.
- M0.6: interface de IA para reconciliação, execução auditável e revisão humana.

Decisões que ficam bloqueadas até M2/M6: provider de IA, tecnologia de fila e backend S3/MinIO. A fundação relacional não depende delas.
