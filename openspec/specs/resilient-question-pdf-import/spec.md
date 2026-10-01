# resilient-question-pdf-import Specification

## Purpose
TBD - created by archiving change resilient-question-pdf-import. Update Purpose after archive.
## Requirements
### Requirement: Retomada idempotente do job PDF
O sistema SHALL recuperar job órfão, preservar checkpoint e não analisar candidato persistido.

#### Scenario: Worker interrompido
- **WHEN** o lease expirar
- **THEN** o job será retomado sem nova questão para candidato existente

### Requirement: Cabeçalho de prova como metadado
O sistema SHALL extrair banca, concurso, cargo e ano de cabeçalho explícito e removê-lo do enunciado.

#### Scenario: Cabeçalho FCC SABESP
- **WHEN** a questão começar com `(FCC – SABESP/Analista de Gestão – Sistemas/2014)`
- **THEN** o cargo será metadado e o cabeçalho não estará no enunciado

### Requirement: Figura associada no ponto semântico
O sistema SHALL persistir figura somente com âncora e marcador únicos no enunciado ou alternativa.

#### Scenario: Figura no enunciado
- **WHEN** houver âncora válida
- **THEN** a figura será renderizada no marcador correspondente

### Requirement: Histórico temporal e desempenho
O histórico SHALL mostrar início e fim e o worker SHALL evitar reanálise e extração visual desnecessária.

#### Scenario: Importação terminal
- **WHEN** a importação terminar, falhar ou for cancelada
- **THEN** início e fim serão exibidos

