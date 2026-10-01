## 1. Contrato e executor

- [x] 1.1 Criar DTOs e porta de análise em lote com resultados indexados por fingerprint.
- [x] 1.2 Adaptar o executor Codex e schema estruturado para entrada e saída de múltiplos candidatos, incluindo imagens isoladas.

## 2. Worker resiliente

- [x] 2.1 Reivindicar checkpoints compatíveis do mesmo job até os limites configuráveis do lote.
- [x] 2.2 Processar resultados, retries e telemetria individualmente a partir de uma chamada em lote.

## 3. Operação e validação

- [x] 3.1 Configurar limites operacionais no Compose e documentar o comportamento do histórico.
- [x] 3.2 Adicionar testes de segmentação de lote, resposta parcial, imagens isoladas e telemetria.
- [x] 3.3 Validar lint, Compose, suíte focal e worker sem jobs pendentes.
