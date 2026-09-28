## ADDED Requirements

### Requirement: Análise assíncrona e idempotente de caderno
O sistema SHALL criar uma execução de análise após finalizar um caderno, sem bloquear sua resposta HTTP, e SHALL processar cada caderno e versão de algoritmo no máximo uma vez.

#### Scenario: Reprocessamento do mesmo caderno
- **WHEN** um job recebe novamente um caderno já concluído na mesma versão
- **THEN** o sistema reutiliza a execução concluída e não altera domínio, agenda ou cards novamente

### Requirement: Contexto mínimo e resposta estruturada do Codex
O worker SHALL enviar ao provider Codex somente resumo necessário de respostas, taxonomia, evidências recentes e cards relacionados, e SHALL validar JSON enumerado antes de qualquer alteração.

#### Scenario: Resposta inválida do provider
- **WHEN** o provider retorna JSON fora do schema permitido
- **THEN** a execução falha de modo auditável e nenhuma ação pedagógica é aplicada

### Requirement: Ações pedagógicas auditáveis
O sistema SHALL registrar caderno, questão quando aplicável, conceito, ação, justificativa curta, confiança, provider e modelo para cada ação aplicada, sem armazenar cadeia de raciocínio.

#### Scenario: Erro conceitual confirmado
- **WHEN** a análise classifica evidências válidas como deficiência conceitual com confiança suficiente
- **THEN** o sistema cria ou reutiliza um card, ou antecipa um existente, e registra a razão estruturada

### Requirement: Questões atualizam domínio e agenda relacionados
O sistema SHALL atualizar sinais de domínio e prioridade de flashcards associados aos assuntos canônicos de questões respondidas, sem tratar todo erro como criação obrigatória de card.

#### Scenario: Erro não pedagógico
- **WHEN** a análise classifica o erro como distração ou interpretação sem lacuna conceitual suficiente
- **THEN** o sistema pode não criar card e registra somente a conclusão auditável
