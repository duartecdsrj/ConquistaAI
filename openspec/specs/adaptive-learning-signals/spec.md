# adaptive-learning-signals Specification

## Purpose
TBD - created by archiving change question-learning-interactions. Update Purpose after archive.
## Requirements
### Requirement: Sinais imutáveis de aprendizagem
O sistema SHALL registrar eventos de aprendizagem para resposta concluída e não domínio, referenciando usuário, questão, tentativa quando aplicável, assunto canônico e instante do evento.

#### Scenario: Resposta concluída
- **WHEN** uma tentativa for concluída
- **THEN** o sistema registra sinal com alternativa final, resultado, tempo gasto, origem e referências necessárias para análise posterior

#### Scenario: Não domínio sem erro
- **WHEN** o usuário marcar não dominei após acertar
- **THEN** o sistema registra sinal distinto de falta de domínio sem reclassificar a resposta como erro

### Requirement: Dados reutilizáveis para adaptação
O sistema SHALL disponibilizar sinais e agregados mínimos por assunto canônico para Performance, Review e análise assíncrona, sem duplicar atributos recuperáveis por relacionamentos.

#### Scenario: Análise de reforço
- **WHEN** um consumidor adaptativo receber sinais de erro, não domínio, tempo ou reincidência
- **THEN** ele recebe referências a assuntos e evidências suficientes para priorizar reforço sem ler dados de outro usuário

### Requirement: Processamento assíncrono desacoplado
O sistema SHALL acumular sinais e despachar análises de aprendizagem por critérios configuráveis, sem chamada de IA em cliques ou respostas síncronas.

#### Scenario: Finalização de caderno
- **WHEN** um caderno for finalizado com novos sinais de aprendizagem
- **THEN** uma análise idempotente é elegível para processamento assíncrono

#### Scenario: Acesso pessoal
- **WHEN** um usuário consultar seus dados de aprendizagem derivados
- **THEN** a consulta é limitada ao próprio usuário
