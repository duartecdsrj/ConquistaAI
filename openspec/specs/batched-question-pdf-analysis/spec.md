# batched-question-pdf-analysis Specification

## Purpose
TBD - created by archiving change batched-question-pdf-analysis. Update Purpose after archive.
## Requirements
### Requirement: Análise estruturada em lote
O sistema SHALL analisar múltiplos candidatos pertencentes ao mesmo job PDF em uma única execução do provider, respeitando limites configuráveis de quantidade e tamanho do payload.

#### Scenario: Lote dentro do limite
- **WHEN** houver checkpoints pendentes contíguos do mesmo job dentro dos limites configurados
- **THEN** o worker enviará todos os candidatos elegíveis em uma única chamada estruturada ao provider

#### Scenario: Limite de payload alcançado
- **WHEN** o próximo candidato exceder o limite de quantidade ou tamanho do lote
- **THEN** ele permanecerá pendente para uma execução posterior sem ser incluído no lote atual

### Requirement: Isolamento de resultados do lote
O sistema SHALL validar e persistir cada resposta pelo fingerprint do candidato, sem permitir que uma resposta inválida, ausente ou duplicada invalide os demais itens válidos do lote.

#### Scenario: Resposta parcialmente inválida
- **WHEN** o provider retornar itens válidos e um item ausente ou inválido
- **THEN** os itens válidos serão concluídos e somente o checkpoint afetado será reenfileirado ou falhará conforme a política de retry

### Requirement: Evidências visuais em lote
O sistema SHALL preservar imagens e marcadores de cada candidato dentro do lote, mantendo arquivos isolados por fingerprint e validando âncoras contra o candidato correspondente.

#### Scenario: Dois candidatos com imagens
- **WHEN** dois candidatos do mesmo lote possuírem ativos com o mesmo índice de página
- **THEN** os arquivos enviados ao provider terão caminhos distintos e as âncoras não poderão cruzar candidatos

