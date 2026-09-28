## Why

A resolução atual registra tentativas e respostas imutáveis, mas não captura intenção de revisão, insegurança, notas privadas, conversas ou problemas percebidos. Sem esses sinais independentes, a ConquistaAI não consegue diferenciar erro de falta de domínio nem produzir reforço adaptativo com segurança.

## What Changes

- Adicionar interações pessoais por usuário e questão: favorito, revisar depois, não dominei e anotação privada editável.
- Adicionar comentários públicos iniciais com resposta encadeável e relatórios de problema auditáveis.
- Preservar tentativas/respostas existentes e complementar seus eventos com contexto de aprendizagem reutilizável por Review e Performance.
- Criar ações, filtros combináveis e barra de ações responsiva nas telas de resolução e visualização completa de questões.
- Adicionar explicação personalizada assíncrona de questão, bloqueada antes da resposta quando poderia revelar gabarito.
- Executar a primeira implementação de IA no mesmo modelo isolado de worker Codex usado pela correção de questões e definir uma porta estruturada para adaptadores futuros de APIs de IA.

## Capabilities

### New Capabilities

- `question-learning-interactions`: marcações independentes, anotações privadas, comentários, relatórios, filtros e experiência de interação da questão.
- `adaptive-learning-signals`: eventos imutáveis e contexto mínimo derivados de tentativas e interações para Performance, Review e análises assíncronas.
- `question-ai-explanations`: solicitações e respostas persistidas de explicação segura, worker Codex inicial e porta de provider estruturado parametrizável.

### Modified Capabilities

Nenhuma; o repositório ainda não possui specs basais.

## Impact
