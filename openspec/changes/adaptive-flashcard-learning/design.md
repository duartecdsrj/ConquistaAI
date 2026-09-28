## Context

Study já persiste cadernos e seleção congelada; Performance preserva tentativas e respostas; Taxonomy possui assuntos canônicos, aliases e vínculos de questão. O corretor de questões já usa um job idempotente, provider configurável e saída estruturada. A mudança introduz Review sem deslocar essas responsabilidades: Taxonomy continua sendo a fonte de conceitos e Performance, a fonte de evidências.

## Goals / Non-Goals

**Goals:**

- Transformar evidências resumidas de caderno em ações pedagógicas auditáveis e idempotentes.
- Manter conteúdo de flashcard reutilizável e estado/histórico estritamente individual.
- Permitir troca do algoritmo de espaçamento e da fórmula de domínio por contratos de domínio.
- Reutilizar o padrão de fila/provider do corretor para uma única chamada estruturada ao Codex por análise.

**Non-Goals:**

- Criar uma taxonomia paralela, inferir ou armazenar cadeia de raciocínio, ou deixar a IA gravar no banco.
- Substituir a curadoria editorial de questões, alterar tentativas imutáveis ou implementar FSRS nesta entrega.
- Enviar todo o histórico pessoal ou o enunciado completo de cada questão ao provider sem necessidade.

## Decisions

### Assuntos canônicos são os conceitos da primeira versão

`taxonomy_subjects` será a identidade de conceito, incluindo sua hierarquia e aliases normalizados. Uma tabela de associação entre questão e assunto canônico já existente será reutilizada; novos cartões terão uma relação principal de conceito e poderão adquirir relações adicionais somente quando a fonte já as declarar. Isso evita duas árvores semanticamente concorrentes.

Alternativa considerada: entidade `concept` nova com sincronização bilateral. Foi descartada pois duplicaria normalização, merge e auditoria que Taxonomy já resolve.

### Conteúdo compartilhado, estado individual e eventos imutáveis

`flashcards` conterá apenas conteúdo pedagógico normalizado, origem e chave semântica determinística. `user_flashcard_progress` conterá estado de agenda/métricas por usuário+card, e `flashcard_reviews` registrará cada classificação com antes/depois. `user_concept_mastery` guardará a visão agregada por usuário+conceito. Índices priorizam fila por usuário, data de revisão e conceito.

Alternativa considerada: copiar cards para cada usuário. Foi descartada por impedir deduplicação e evolução editorial do conteúdo base.

### Estratégias substituíveis para agenda, domínio e prioridade

`SpacedRepetitionStrategyInterface`, `MasteryEstimatorInterface` e `ReviewPriorityCalculatorInterface` ficarão no domínio. A estratégia inicial centraliza intervalos e aplica Não lembrei/Difícil/Lembrei/Fácil; o estimador combina qualidade das revisões, desempenho relacionado, recência e dificuldade em 0–100; prioridade combina atraso, lacuna, relevância disponível e proximidade de prova. Configuração fica em value object/configuração única, não em controladores ou repositórios.

Alternativa considerada: fórmula dentro de cada serviço. Foi descartada porque inviabiliza FSRS e calibração independente.

### IA segue o pipeline do corretor, sem escrita direta

Ao terminar o caderno, Study cria ou reutiliza uma execução de análise única pelo `notebook_id` e versão do algoritmo. Um worker, no mesmo formato do corretor de questões, monta um resumo mínimo e chama o provider Codex configurado. O provider devolve JSON validado contra DTO/schema com ações enumeradas. O aplicador transacional traduz somente ações permitidas para entidades Review e grava auditoria curta, provider/modelo/duração/tokens/retries. Falha ou JSON inválido deixa a execução recuperável e não altera domínio.

Alternativa considerada: chamada síncrona ao finalizar. Foi descartada por bloquear o usuário e aumentar a fragilidade da experiência.

### Deduplicação determinística antes e depois da IA

O gerador normaliza texto de frente/verso/tipo e usa fingerprint versionada por conceito. O aplicador busca equivalência exata normalizada antes de criar; ações da IA apenas referenciam IDs candidatos válidos ou conteúdo validado. A única execução concluída por caderno+versão evita aplicar desempenho duas vezes.

Alternativa considerada: deixar a IA decidir duplicidade por linguagem natural. Foi descartada por não ser auditável nem determinística.

## Risks / Trade-offs

- [Classificação imprecisa da IA] → ações enumeradas, schema estrito, limites por análise, explicação curta e validação determinística.
- [Custo/volume de chamadas] → uma chamada por execução idempotente, resumo limitado, telemetria e retentativas limitadas.
- [Ausência de worker ativo] → resultado do caderno permanece disponível; execução fica pendente/recuperável por comando do mesmo worker.
- [Baixa amostra] → domínio permanece com confiança limitada e a IA pode devolver nenhuma ação.
- [Conteúdo de questão defeituosa] → prompt inclui estado/gabarito/contexto editorial disponível e proíbe cartão quando houver dúvida de validade.

## Migration Plan

1. Criar tabelas e índices aditivos, sem alterar dados existentes.
2. Implantar contratos e worker desativado por configuração segura; a finalização de caderno nunca dependerá do worker.
3. Implantar API e SPA de revisão; habilitar o worker Codex após configurar provider/modelo.
4. Rollback desabilita rotas/worker, preservando registros para retomada; não remove histórico ou progresso.

## Open Questions

- Confirmar o limite operacional diário de análises Codex e a política de retentativa antes da ativação em produção.
- A relevância de edital e proximidade da prova depende de data de prova hoje opcional; enquanto ausente, o fator neutro será usado.
