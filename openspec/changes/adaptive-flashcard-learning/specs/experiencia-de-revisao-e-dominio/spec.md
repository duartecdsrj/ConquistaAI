## ADDED Requirements

### Requirement: Sessão de revisão responsiva
A SPA SHALL exibir cartões reais da sessão com frente inicialmente oculta, revelação discreta, classificação de recordação, progresso, conceito e saída retomável em desktop e mobile.

#### Scenario: Revelar resposta e revisar
- **WHEN** o usuário toca ou clica em um card da sessão
- **THEN** a resposta é revelada e as quatro classificações de recordação ficam disponíveis

### Requirement: Resultado de análise do caderno
A SPA SHALL apresentar o resultado persistido da análise de caderno, incluindo acertos, pontos identificados e ações reais, sem inventar dados enquanto a execução estiver pendente.

#### Scenario: Análise ainda processando
- **WHEN** o usuário visualiza um caderno cuja análise ainda não terminou
- **THEN** a interface informa processamento e consulta somente o estado real retornado pela API

### Requirement: Mapa de domínio por hierarquia
A SPA SHALL permitir navegar nos assuntos canônicos e exibir domínio individual agregado com indicação de confiança/amostra quando existir.

#### Scenario: Dados insuficientes
- **WHEN** o usuário não possui evidências suficientes em um conceito
- **THEN** a interface indica dados insuficientes em vez de afirmar domínio baixo
