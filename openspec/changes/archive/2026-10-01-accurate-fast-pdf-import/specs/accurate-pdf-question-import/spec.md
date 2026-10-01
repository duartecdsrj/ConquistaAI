## ADDED Requirements

### Requirement: Seleção determinística de múltipla escolha com gabarito
O sistema SHALL criar candidato de importação somente para bloco que possua alternativas A-E e marcador explícito `Gabarito: Letra <A-E>`.

#### Scenario: Questão objetiva com gabarito
- **WHEN** o PDF contiver alternativas A-E e `Gabarito: Letra C`
- **THEN** o bloco delimitado será enviado ao analisador com o gabarito de referência disponível na evidência

#### Scenario: Item Certo ou Errado
- **WHEN** o PDF contiver `Gabarito: Certo`, `Correto` ou `Errado` sem alternativas A-E
- **THEN** o item não será enviado ao analisador de múltipla escolha nem consumirá retentativa

### Requirement: Falha de candidato sem atraso indevido
O sistema SHALL encerrar falha determinística de conteúdo no candidato afetado e SHALL reservar retentativa com backoff apenas para indisponibilidade transitória do provider.

#### Scenario: Resposta estruturalmente inválida
- **WHEN** a resposta do analisador violar o contrato de alternativas, páginas ou achados
- **THEN** o checkpoint será terminal com motivo seguro e os demais candidatos continuarão

#### Scenario: Deadlock ao reivindicar trabalho
- **WHEN** houver deadlock na reivindicação concorrente
- **THEN** o consumidor encerrará a iteração sem fechar o EntityManager e tentará trabalho novo no próximo ciclo

### Requirement: Histórico reconciliado
O sistema SHALL apresentar no histórico o total de candidatos processados, criados, duplicados e rejeitados a partir dos resultados persistidos.

#### Scenario: Rejeição durante escrita
- **WHEN** uma análise for concluída mas sua escrita de questão falhar
- **THEN** o histórico incrementará o total com erro sem ocultar o candidato processado
