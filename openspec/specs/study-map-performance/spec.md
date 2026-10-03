# study-map-performance Specification

## Purpose
TBD - created by archiving change study-map-performance. Update Purpose after archive.
## Requirements
### Requirement: Consulta autenticada do mapa e do cronograma por concurso
O sistema SHALL fornecer `GET /performance/study-map` somente ao usuário autenticado. A rota MUST exigir `exam_id`, aceitar opcionalmente `from` e `to` em formato de data ISO e retornar o envelope padrão com árvore, métricas e cronograma do próprio usuário.

#### Scenario: Consulta válida por concurso
- **WHEN** o usuário autenticado informa um concurso válido
- **THEN** a API retorna os nós aplicáveis, as estatísticas próprias e os itens de cronograma do recorte

#### Scenario: Intervalo inválido
- **WHEN** `from` for posterior a `to` ou uma data for inválida
- **THEN** a API responde `422 VALIDATION_FAILED` sem consultar estatísticas

#### Scenario: Concurso sem dados próprios
- **WHEN** o usuário consulta um concurso sem práticas ou planejamento próprio
- **THEN** a API retorna mapa e cronograma vazios sem expor dados de outro usuário

### Requirement: Hierarquia de assuntos e subassuntos
O mapa SHALL retornar nós canônicos com `id`, `parentId`, `name`, `depth` e `children`, preservando a relação entre assuntos principais e subassuntos aplicáveis ao concurso.

#### Scenario: Assunto com subassuntos
- **WHEN** a matriz do concurso possui um assunto principal com descendentes
- **THEN** o nó principal retorna seus subassuntos em ordem estável e mantém a hierarquia completa

#### Scenario: Nó sem prática
- **WHEN** um assunto aplicável não possuir resposta final concluída no recorte
- **THEN** o nó é retornado com estado `NO_DATA` e sem taxa de acerto inferida

### Requirement: Métricas reais agregadas por nó
Cada nó SHALL expor `answered`, `correct`, `incorrect`, `accuracy`, `distinctDays` e `evidenceStatus`, calculados exclusivamente a partir das respostas finais de tentativas concluídas do usuário no recorte. Métricas de um pai MUST agregar seus descendentes sem contar duas vezes a mesma resposta no mesmo nó.

#### Scenario: Estatísticas de subassunto
- **WHEN** o usuário conclui práticas classificadas no subassunto
- **THEN** o subassunto reflete seus acertos e erros reais e o assunto pai incorpora esses resultados

#### Scenario: Pergunta associada a mais de um nó
- **WHEN** uma resposta final estiver vinculada a múltiplos assuntos que compartilham um ancestral
- **THEN** ela é contabilizada uma única vez naquele ancestral

### Requirement: Cronograma Gantt persistido
O sistema SHALL permitir ao usuário criar e atualizar, para cada assunto aplicável ao concurso, um item de cronograma com `startDate`, `endDate`, `status` e `completedAt` opcional. O mapa MUST retornar esses itens sem alterar a posição temporal de itens concluídos.

#### Scenario: Agendamento de subassunto
- **WHEN** o usuário informa assunto, data inicial e data final válidos
- **THEN** o sistema persiste o item no seu cronograma e o retorna na faixa temporal correspondente

#### Scenario: Conclusão preserva posição
- **WHEN** o usuário marca um item como `STUDIED` ou `COMPLETED`
- **THEN** o sistema registra o estado e a conclusão sem remover o assunto da árvore ou mudar suas datas planejadas

#### Scenario: Datas inválidas
- **WHEN** a data final for anterior à inicial
- **THEN** o sistema responde `422 VALIDATION_FAILED` e preserva o cronograma confirmado

### Requirement: Dependências entre assuntos planejados
O sistema SHALL permitir dependências direcionadas entre itens do mesmo usuário e concurso. A API MUST expor predecessores e sucessores e rejeitar ligações que criem ciclos ou referenciem assunto fora do cronograma.

#### Scenario: Dependência válida
- **WHEN** o usuário vincula um subassunto planejado a um predecessor do mesmo concurso
- **THEN** o cronograma retorna a relação e a interface a indica nos dois itens

#### Scenario: Dependência cíclica
- **WHEN** o usuário tenta criar uma dependência que fecha um ciclo
- **THEN** o sistema responde `422 VALIDATION_FAILED` sem gravar a ligação

### Requirement: Experiência responsiva do mapa de estudo
A SPA SHALL apresentar o mapa na área Performance com seleção de concurso, período, resumo de práticas, árvore expansível e escala Gantt. A página MUST consumir somente o contrato do mapa pelas camadas Performance e tratar carregamento, erro e vazio.

#### Scenario: Expansão de assunto principal
- **WHEN** o usuário expande um assunto principal
- **THEN** a interface apresenta seus subassuntos, métricas reais e faixas de cronograma sem recalcular dados de domínio no cliente

#### Scenario: Uso em tela pequena
- **WHEN** o mapa for aberto em largura de até 599 px
- **THEN** os controles e as linhas são reorganizados para uma coluna, mantendo acesso à escala temporal e às ações de toque

#### Scenario: Sem práticas no período
- **WHEN** não houver respostas concluídas no período selecionado
- **THEN** a interface informa a ausência de dados e não exibe barras ou percentuais como se fossem progresso real

