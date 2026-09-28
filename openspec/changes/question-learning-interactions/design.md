## Context

`Attempt` e `Answer` já preservam o histórico de resolução por caderno, e Review já concentra cartões, domínio e worker de análise. Faltam interações independentes por usuário/questão e uma fronteira segura para explicações de IA.

## Goals / Non-Goals

**Goals:**

- Registrar marcações independentes, notas privadas, comentários públicos, denúncias e sinais de aprendizagem sem alterar o histórico de tentativas.
- Reutilizar Performance para resultados e tempos, Taxonomy para conceitos e Review para reforço adaptativo.
- Executar explicações em job persistido com Codex inicialmente e providers de API substituíveis.
- Entregar a mesma experiência nas resoluções de caderno e nas visualizações completas de questão.

**Non-Goals:**

- Não definir uma fórmula final de domínio nem gerar flashcards em cada interação.
- Não implementar moderação, curtidas, ordenação avançada ou perfis verificados de comentários nesta etapa.
- Não revelar gabarito ou explicação corretiva antes de uma resposta que permita isso.

## Decisions

### Interações são relações, não status único

Uma tabela de estado por `user_id` e `question_id` guarda favorito, revisão e não domínio como flags independentes. Anotações, comentários e relatórios usam tabelas próprias; indicadores como `TEM_ANOTACAO` são derivados por consulta. Isso preserva filtros combináveis e evita inconsistência de campos derivados.

Alternativa descartada: um enum de status por questão. Ele não representa combinações simultâneas e confundiria resultado da tentativa com intenção do estudante.

### Tentativas continuam a fonte do resultado

`Attempt`/`Answer` permanecem imutáveis. Uma projeção de sinal de aprendizagem referencia tentativa, questão, assunto e interação, guardando apenas valores que não podem ser recuperados de modo estável no futuro. Eventos são gravados na mesma transação do caso de uso e consumidos assincronamente.

Alternativa descartada: recalcular toda a história em cada clique. Isso aumenta custo e não preserva o instante da intenção do usuário.

### IA por porta estruturada e execução persistida

`QuestionExplanationProviderInterface` recebe contexto mínimo tipado e devolve schema validado. O primeiro adaptador executa Codex no mesmo isolamento do worker de correção; adaptadores HTTP OpenAI, Gemini ou outros implementam a mesma porta e são escolhidos por configuração. Solicitações têm estado, tentativas, telemetria sanitizada e resposta persistida; prompt/resposta bruta não entram em logs nem no banco.

Alternativa descartada: chamar IA dentro do endpoint. Isso bloqueia a resolução, dificulta retentativas e acopla o domínio a um fornecedor.

### UX é otimista somente para flags reversíveis

Favorito, revisar depois e não dominei atualizam a barra imediatamente com rollback no erro. Nota abre painel lateral editável; comentários, relatório e explicação usam drawer/bottom sheet, sem sair da resolução. Mobile mantém favorito, revisão e não domínio visíveis e agrupa ações secundárias.
