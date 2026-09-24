# Cronograma de desenvolvimento — ConquistaAI

Atualizado em 24/09/2026. Cada marco só avança quando backend, frontend, documentação e testes proporcionais estiverem concluídos.

| Marco | Entrega integrada | Dependências | Critério de aceite | Situação |
|---|---|---|---|---|
| M0 | Taxonomia canônica: árvore, aliases, prevenção de ciclos e revisão de duplicidade | Nenhuma | assunto global, aliases e auditoria funcionais | Concluído |
| M1 | Concursos, cargos e editais estruturados | M0 | PDF preservado, hash idempotente e conteúdo por cargo | Concluído |
| M2 | Jobs e processamento de edital | M1 | extração assíncrona com progresso e proveniência | Concluído |
| M3 | Questões reais, fontes e deduplicação | M0, M1 | importação idempotente e revisão de candidatos | Concluído |
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
- M0.2: domínio Taxonomy, slug/normalização, operações de árvore e prevenção de ciclo. **Concluída.**
- M0.3: API administrativa, testes e tela Quasar. **Concluída.**
- M0.4: árvore de assuntos, aliases, mover e editar no frontend.
- M0.5: sugestões de duplicidade, proposta de fusão e reatribuição transacional. **Concluída: revisão administrativa, reatribuição idempotente, desativação da origem e auditoria.**
- M0.6: interface de IA para reconciliação, execução auditável e revisão humana. **Concluída com adaptador determinístico local, propostas revisáveis e fusão administrativa auditada.**

Decisões que ficam bloqueadas até M2/M6: provider de IA, tecnologia de fila e backend S3/MinIO. A fundação relacional não depende delas.

## M1 — sequência detalhada

- M1.1: concursos, cargos e editais administrativos. **Concluída anteriormente.**
- M1.2: preservação do PDF do edital, metadados seguros, hash SHA-256 idempotente e volume persistente. **Concluída — commit `e731c56`.**
- M1.3: conteúdo programático por assunto, com trecho, página e offsets de proveniência. **Concluída — commit `e26952b`.**
- M1.4: vínculo explícito e revisável entre `subjects` locais e `taxonomy_subjects` canônicos. **Concluída — commit `ac4d826`.** Não cria assuntos, não funde taxonomia e não altera questões.
- M1.5: validação final do catálogo. **Concluída em 24/09/2026:** migrations `007` a `009` confirmadas no MySQL local, contrato documentado, PHPUnit com 34 testes e 84 assertions, build Quasar aprovado e layout do catálogo adaptado para uma coluna até 600 px.

**Critério de saída do M1:** um administrador cria concurso/cargo/edital, preserva o PDF, estrutura assuntos com origem rastreável e os associa conscientemente à taxonomia canônica. Só então M2 pode processar conteúdo automaticamente.

## M2 — sequência planejada

- M2.1: fila/worker desacoplado e estado de processamento do edital. **Concluída — fila persistida no MySQL e worker Compose independente.**
- M2.2: extração de texto do PDF com páginas, offsets e hash de entrada. **Concluída com `pdftotext`, hash e proveniência por página.**
- M2.3: persistência de resultados e erros com progresso consultável. **Concluída.**
- M2.4: revisão administrativa do conteúdo extraído e reprocessamento idempotente. **Concluída.**

## M3 a M7 — sequências planejadas

- **M3:** fontes de questões, importação idempotente, candidatos duplicados, revisão e publicação. **Concluído.**
- **M4:** agregações hierárquicas por assunto, dashboard por edital e indicação de dados insuficientes.
- **M5:** cadernos inteligentes, filtros explicáveis, metas e plano de estudo.
- **M6:** providers de IA desacoplados, RAG de edital, evidências e revisão humana.
- **M7:** providers permitidos de descoberta web, catálogo de provas/gabaritos, proveniência e limites operacionais.
