## ADDED Requirements

### Requirement: Sala privada de Duelo
O sistema SHALL criar sala privada autenticada com código curto, configurações permitidas e participantes que escolhem exatamente os assuntos exigidos.

#### Scenario: Entrada válida
- **WHEN** usuário entra por código disponível
- **THEN** o sistema o registra como participante aguardando escolhas

### Requirement: Questões e resolução autoritativas
O sistema SHALL iniciar apenas com participantes prontos, congelar sequência PUBLISHED equilibrada e aceitar uma resposta única antes do prazo do servidor.

#### Scenario: Resposta duplicada
- **WHEN** o participante envia outra alternativa para a mesma questão
- **THEN** somente a primeira resposta é persistida

### Requirement: Pontuação e resultado
O sistema SHALL encerrar quando todos respondem ou expira o prazo, pontuar corretas por ordem 100/75/50/25, erradas em -25 e persistir classificação final.

#### Scenario: Correções ordenadas
- **WHEN** quatro respostas corretas são recebidas
- **THEN** o placar concede 100, 75, 50 e 25 em ordem determinística
