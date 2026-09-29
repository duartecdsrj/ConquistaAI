## ADDED Requirements

### Requirement: Analisador estruturado e configurável de importação
O sistema SHALL processar cada questão candidata de um PDF por uma porta de análise estruturada selecionada por configuração, usando Codex em worker isolado como implementação inicial e sem acoplar o caso de uso ao executável do provider.

#### Scenario: Codex selecionado
- **WHEN** um job de PDF pendente for reivindicado com o analisador `codex` ativo
- **THEN** o worker analisará cada questão candidata em workspace isolado e persistirá somente resposta validada pelo schema

#### Scenario: Provider alternativo selecionado
- **WHEN** a configuração selecionar um adaptador compatível diferente de Codex
- **THEN** o mesmo caso de uso consumirá a resposta pelo schema de análise sem depender de detalhes daquele adaptador

#### Scenario: Provider indisponível
- **WHEN** o analisador não puder produzir resposta válida
- **THEN** o job seguirá a política persistida de retentativa ou falha e não usará silenciosamente o extrator sem análise

### Requirement: Análise completa antes da persistência editorial
O sistema SHALL validar uma análise individual de enunciado, alternativas, estrutura de correlação/afirmações, metadados, gabarito e imagens antes de criar a questão ou gravar seu histórico editorial.

#### Scenario: Questão objetiva válida
- **WHEN** a análise identificar enunciado, quatro ou cinco alternativas distintas, estrutura e taxonomia válidas
- **THEN** o escritor aplicará as regras existentes de sanitização, duplicidade e classificação antes de criar a questão em revisão

#### Scenario: Correlação ou afirmações
- **WHEN** a questão contiver colunas a correlacionar ou afirmações numeradas
- **THEN** a análise preservará grupos, ordem, referências e alternativas sem colapsar a estrutura em texto comum

#### Scenario: Metadado sem evidência
- **WHEN** concurso, ano, banca ou cargo não estiverem explicitamente comprovados no bloco ou nas páginas de evidência
- **THEN** o campo será nulo e o analisador não inventará o metadado

### Requirement: Verificação de gabarito e coerência visual
O sistema SHALL registrar achados tipados para possível divergência de gabarito, estrutura, metadados e imagens, sem alterar automaticamente a questão ou a resposta correta.

#### Scenario: Possível conflito de gabarito
- **WHEN** a análise encontrar evidência de que o gabarito informado é incoerente com enunciado, alternativas ou fonte
- **THEN** persistirá `ANSWER_KEY_CONFLICT`, manterá a questão em `REVIEW` e não modificará o gabarito automaticamente

#### Scenario: Figura coerente e ancorada
- **WHEN** uma figura necessária estiver associada inequivocamente ao enunciado ou a uma alternativa
- **THEN** o ativo será persistido nessa posição e a proveniência de página será registrada

#### Scenario: Discrepância de imagem
- **WHEN** uma imagem referenciada não estiver presente, não corresponder ao conteúdo ou não puder ser ancorada com segurança
- **THEN** persistirá `IMAGE_DISCREPANCY` e o ativo não será associado automaticamente

### Requirement: Histórico auditável e telemetria de consumo
O sistema SHALL manter análise imutável por questão e agregados por job, incluindo evidências, achados, provider, modelo, duração e uso de tokens.

#### Scenario: Uso fornecido pelo provider
- **WHEN** o provider retornar contagens de tokens
- **THEN** o histórico persistirá tokens de entrada, saída e total, além do custo quando informado, e agregará esses valores no job

#### Scenario: Uso indisponível
- **WHEN** o provider não retornar uso confiável
- **THEN** o histórico indicará explicitamente indisponibilidade e não exibirá estimativa como consumo real

#### Scenario: Consulta administrativa do job
- **WHEN** um administrador consultar o histórico de um PDF
- **THEN** receberá totais de questões, achados por tipo, provider/modelo, duração e telemetria permitida do job
