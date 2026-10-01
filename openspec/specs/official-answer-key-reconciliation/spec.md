# official-answer-key-reconciliation Specification

## Purpose
TBD - created by archiving change resilient-parallel-pdf-import. Update Purpose after archive.
## Requirements
### Requirement: Promoção de gabarito oficial em duplicata
O sistema SHALL comparar o gabarito de uma questão duplicada antes de descartá-la e SHALL promover exclusivamente uma alternativa com evidência `OFFICIAL` quando a questão existente não possuir gabarito ou estiver marcada como `AI_ESTIMATED`.

#### Scenario: Duplicata contém gabarito oficial
- **WHEN** uma questão duplicada tiver alternativa válida com fonte `OFFICIAL` e a existente não for oficial
- **THEN** a alternativa correspondente da questão existente será definida como correta com fonte `OFFICIAL` e a importação registrará a duplicata

#### Scenario: Duplicata contém somente estimativa
- **WHEN** uma questão duplicada trouxer gabarito `AI_ESTIMATED`
- **THEN** o gabarito e a fonte existentes não serão modificados

#### Scenario: Gabarito oficial já existe
- **WHEN** uma questão duplicada encontrar uma questão com fonte `OFFICIAL`
- **THEN** nenhuma alternativa, conteúdo ou metadado editorial existente será substituído

