## Context

O painel Performance já consolida tentativas concluídas por edital ou concurso, mas não forma uma árvore navegável nem oferece calendário. A referência combina uma estrutura de assuntos com faixas temporais; o novo módulo precisa preservar essa leitura e separar resultados observados do planejamento manual persistido.

## Goals / Non-Goals

**Goals:**

- Entregar uma consulta autenticada por concurso que devolva a árvore canônica aplicável, métricas agregadas e cronograma do usuário.
- Permitir criar e atualizar itens de cronograma com assunto, início, fim, estado e dependências explícitas.
- Mostrar assuntos e subassuntos em um Gantt, mantendo itens concluídos na mesma posição temporal.
- Calcular totais, acertos, erros, taxa de acerto e dias distintos exclusivamente a partir da resposta final de cada tentativa concluída.

**Non-Goals:**

- Não inferir datas, duração ou dependências automaticamente; esses dados serão planejados explicitamente pelo usuário.
- Não alterar tentativas, respostas ou taxonomia existentes.
- Não inferir desempenho para nós sem práticas; eles serão apresentados como sem dados.

## Decisions

### Mapa de leitura e cronograma persistido

`GET /performance/study-map` retornará o mapa, as métricas reais e os itens do cronograma para um `exam_id`. `PUT /performance/study-map/schedule` criará ou atualizará um item planejado e `PATCH /performance/study-map/schedule/{subjectId}` atualizará seu estado, datas ou dependências. Somente o usuário autenticado poderá ler ou alterar seu próprio cronograma.

Alternativa considerada: manter o cronograma apenas no navegador. Foi descartada porque a posição, conclusão e calendário precisam sobreviver a dispositivos e sessões.

### Agregação por ancestral canônico

Cada resposta final concluída será associada aos assuntos canônicos da questão e propagada para ancestrais dentro da árvore aplicável ao concurso. Um pai refletirá a soma dos descendentes; uma resposta ligada a mais de um assunto contará uma vez por ancestral.

Alternativa considerada: calcular somente folhas. Foi descartada porque impediria que assuntos principais exibissem o desempenho consolidado pedido.

### Dependências explícitas e sem ciclos

Dependências serão ligações direcionadas entre itens do mesmo cronograma e concurso. O backend validará existência, escopo do usuário e ausência de ciclo. A interface exibirá predecessores e sucessores, mas não recalculará datas automaticamente na primeira entrega.

Alternativa considerada: inferir pré-requisitos pela taxonomia. Foi descartada porque a relação pai-filho não define, de forma confiável, ordem pedagógica.

### Conclusão separada de desempenho e posição

Um item pode receber estado `PLANNED`, `STUDIED` ou `COMPLETED` e data de conclusão, independentemente das métricas de prática. A conclusão não remove nem desloca a faixa Gantt; apenas muda seu tratamento visual e conserva a data planejada.

Alternativa considerada: usar taxa de acerto como conclusão automática. Foi descartada porque desempenho observado e decisão de estudo são conceitos distintos.

### Integração no módulo Performance

A SPA seguirá `PerformancePage -> usePerformance -> PerformanceUseCases -> PerformanceRepository -> AxiosPerformanceRepository`. A expansão da árvore e a escala temporal são estado local de apresentação; cronograma, dependências e conclusão confirmados vêm da API.

## Risks / Trade-offs

- [Árvores grandes aumentam o custo da agregação] → limitar consulta ao concurso e agregar respostas finais uma vez antes de montar a árvore.
- [Dependências cíclicas bloqueiam leitura do plano] → validar ciclo na escrita e rejeitar com `422 VALIDATION_FAILED`.
- [Datas conflitantes com predecessores] → exibir conflito e permitir ajuste manual; a primeira entrega não deslocará atividades automaticamente.
- [Questões sem taxonomia não aparecem em ramos] → o resumo informará respostas sem classificação, sem atribuí-las artificialmente.

## Migration Plan

1. Criar tabelas de itens de cronograma e dependências com chaves para usuário, concurso e taxonomia.
2. Publicar rotas de leitura e escrita autenticadas junto à SPA.
3. Em rollback, remover a interface e as rotas; os registros de planejamento permanecem preservados até uma migração reversível explícita.

## Open Questions

- Nenhuma para a primeira entrega: datas, dependências e conclusão são informadas pelo usuário; não haverá reagendamento automático.
