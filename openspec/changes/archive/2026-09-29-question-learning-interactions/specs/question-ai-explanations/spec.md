## ADDED Requirements

### Requirement: Solicitação segura de explicação de questão
O sistema SHALL permitir que o usuário autenticado solicite uma explicação personalizada de uma questão somente com contexto permitido pelo estado da tentativa.

#### Scenario: Questão ainda não respondida
- **WHEN** o usuário pedir explicação corretiva antes de concluir uma tentativa
- **THEN** o sistema não revela alternativa correta, gabarito ou justificativa que permita inferi-los

#### Scenario: Questão respondida
- **WHEN** o usuário pedir explicação após concluir uma tentativa
- **THEN** o contexto inclui questão, alternativas, escolha final, resultado, assuntos, não domínio e histórico mínimo permitido do próprio usuário

### Requirement: Execução persistida de explicação
O sistema SHALL persistir solicitação, estado, tentativas limitadas, provider, modelo, telemetria sanitizada e resultado seguro da explicação.

#### Scenario: Requisição idempotente
- **WHEN** houver solicitação pendente ou concluída para o mesmo usuário, questão, tentativa e versão de algoritmo
- **THEN** o sistema reutiliza a execução existente e não cria trabalho duplicado

#### Scenario: Falha de provider
- **WHEN** o provider retornar conteúdo fora do schema ou falhar
- **THEN** a execução fica em falha segura ou aguarda retentativa limitada sem expor detalhes internos ao usuário

### Requirement: Provider Codex inicial e adaptadores parametrizáveis
O sistema SHALL usar inicialmente um worker Codex isolado no padrão do worker de correção de questões e SHALL depender de uma porta estruturada para providers externos configuráveis.

#### Scenario: Worker Codex configurado
- **WHEN** uma execução pendente for reivindicada com o provider Codex ativo
- **THEN** o worker executa em ambiente isolado, valida JSON estruturado e persiste somente a resposta permitida e telemetria sanitizada

#### Scenario: Provider HTTP alternativo
- **WHEN** a configuração selecionar um adaptador de API compatível
- **THEN** o mesmo caso de uso recebe resposta no schema definido sem depender de detalhes do Codex

### Requirement: Explicação orientada à aprendizagem
O sistema SHALL retornar explicação que relacione conceito, alternativa correta e escolha do usuário quando permitidos, sem copiar a questão como flashcard.

#### Scenario: Erro ou insegurança recorrente
- **WHEN** sinais recentes indicarem erro ou não domínio no mesmo conceito
- **THEN** a explicação identifica o tópico de reforço e produz saída estruturada reutilizável pela análise adaptativa
