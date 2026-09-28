## ADDED Requirements

### Requirement: Navegação livre entre questões
O Caderno SHALL permitir avançar ou retornar entre questões sem exigir resposta registrada.

#### Scenario: Próxima questão pendente
- **WHEN** o usuário acionar próxima questão sem responder a atual
- **THEN** o Caderno exibe a questão seguinte da seleção congelada

### Requirement: Navegação entre assuntos
O Caderno SHALL oferecer assunto anterior e próximo assunto quando existirem grupos distintos na seleção.

#### Scenario: Próximo assunto disponível
- **WHEN** houver questão posterior com assunto canônico primário diferente
- **THEN** o Caderno avança para a primeira questão desse assunto
