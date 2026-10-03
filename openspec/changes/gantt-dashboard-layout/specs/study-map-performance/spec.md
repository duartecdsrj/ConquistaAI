## ADDED Requirements

### Requirement: Dados persistidos para indicadores de planejamento
O sistema SHALL permitir que o usuário autenticado mantenha a data da prova do seu mapa por concurso e a estimativa de tempo de cada item planejado. O contrato do mapa MUST expor total estimado, tempo concluído, tempo restante, dias até a prova e percentual geral, calculados somente a partir desses dados persistidos.

#### Scenario: Indicadores com carga e prova informadas
- **WHEN** o usuário possuir itens com estimativas e uma data de prova no mapa do concurso
- **THEN** a API retorna os totais de planejamento e os dias restantes até a prova sem usar duração de calendário como estimativa

#### Scenario: Plano sem estimativa
- **WHEN** um item de cronograma existente não possuir estimativa informada
- **THEN** ele permanece no cronograma e os indicadores identificam a carga como não informada sem inventar horas

### Requirement: Indicadores laterais reais do mapa
O sistema SHALL retornar a distribuição da carga estimada por assunto principal e os indicadores de desempenho por assunto, calculados a partir das estimativas persistidas e das respostas finais do usuário, respectivamente. Assuntos sem evidência de prática MUST permanecer sem classificação de força ou fraqueza.

#### Scenario: Distribuição da carga planejada
- **WHEN** o mapa possuir itens estimados em mais de um assunto principal
- **THEN** a API retorna a participação de cada assunto no total estimado para a visualização de distribuição

#### Scenario: Desempenho sem evidência
- **WHEN** um assunto não tiver respostas finais no período selecionado
- **THEN** ele é retornado como sem evidência e não recebe percentual de acerto inventado

### Requirement: Painel de cronograma em Gantt
A SPA SHALL apresentar o cronograma selecionado em um painel com cartões de resumo, navegação de período, filtros, árvore de assuntos, coluna de carga estimada, progresso e escala Gantt. A interface MUST manter visível no calendário um assunto estudado ou concluído, nas mesmas datas planejadas.

#### Scenario: Navegação do período
- **WHEN** o usuário avança, retorna ou seleciona o período do cronograma
- **THEN** a escala Gantt e as faixas exibidas são atualizadas para o intervalo selecionado sem alterar itens persistidos

#### Scenario: Conclusão mantém a faixa
- **WHEN** o usuário altera um item planejado para estudado ou concluído
- **THEN** a linha e a faixa temporal do item continuam presentes no Gantt com sua indicação de estado

### Requirement: Coluna lateral de indicadores
Em telas amplas, a SPA SHALL exibir uma coluna lateral ao Gantt com progresso geral, distribuição da carga por disciplina, pontos fortes e fracos e legenda. Em telas pequenas, esses indicadores MUST continuar acessíveis em blocos empilhados.

#### Scenario: Painel em tela ampla
- **WHEN** a largura disponível comportar o cronograma e a coluna lateral
- **THEN** os indicadores são exibidos à direita da grade Gantt, sem sobrepor a programação

#### Scenario: Painel em celular
- **WHEN** o cronograma for aberto em largura de até 599 px
- **THEN** os indicadores são exibidos abaixo ou acima da grade e os controles permanecem utilizáveis
