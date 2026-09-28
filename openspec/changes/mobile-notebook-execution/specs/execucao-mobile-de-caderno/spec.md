## ADDED Requirements

### Requirement: Vistas móveis do caderno
Em telas de até 599 px, a execução de caderno SHALL apresentar as vistas Questão, Navegação e Progresso em abas acessíveis, mantendo a questão atual e seus dados de progresso ao alternar entre elas.

#### Scenario: Abertura da navegação por aba
- **WHEN** a pessoa selecionar a aba Navegação no caderno mobile
- **THEN** o seletor e a grade de questões SHALL ser exibidos sem remover a questão atual

#### Scenario: Abertura do progresso por aba
- **WHEN** a pessoa selecionar a aba Progresso no caderno mobile
- **THEN** as estatísticas reais do caderno e a ação de finalização SHALL ser exibidas

### Requirement: Troca de vista por arrasto
A execução móvel SHALL trocar a vista ativa quando receber um arrasto horizontal predominante de pelo menos 48 px, sem trocar de vista em uma rolagem predominantemente vertical.

#### Scenario: Arrasto para a próxima vista
- **WHEN** a pessoa arrastar a área de vistas para a esquerda a partir da vista Questão
- **THEN** a aba Navegação SHALL se tornar ativa

#### Scenario: Rolagem do enunciado
- **WHEN** a pessoa rolar verticalmente um enunciado longo
- **THEN** somente o conteúdo da questão SHALL rolar e a vista ativa SHALL permanecer Questão

### Requirement: Controles sempre visíveis durante a leitura
Na vista Questão móvel, o enunciado, alternativas e avisos SHALL rolar em área interna, enquanto os controles de resposta e navegação SHALL permanecer visíveis no rodapé da vista. O cabeçalho SHALL exibir ícones funcionais para pausar/retomar e finalizar.

#### Scenario: Questão longa
- **WHEN** a questão tiver conteúdo maior que a altura disponível
- **THEN** a pessoa SHALL conseguir rolar o conteúdo sem perder os botões Anterior, confirmar resposta ou Próxima questão

#### Scenario: Cabeçalho compacto
- **WHEN** o caderno for aberto em tela mobile
- **THEN** os botões de pausar/retomar e finalizar SHALL manter ícones visíveis e nomes acessíveis

### Requirement: Continuidade e bloqueio da execução
O caderno SHALL persistir a questão ativa do proprietário e SHALL aceitar tentativas e respostas somente no estado `IN_PROGRESS`.

#### Scenario: Retomada após pausa ou fechamento
- **WHEN** a pessoa reabrir um caderno pausado, interrompido ou fechado na questão N
- **THEN** a execução SHALL abrir a questão N e permitir resposta somente após retomar o caderno

#### Scenario: Tentativa enquanto pausado
- **WHEN** uma pessoa ou cliente enviar uma tentativa ou resposta para caderno `PAUSED`
- **THEN** a API SHALL rejeitar a operação com `409 STATE_CONFLICT` e a interface SHALL manter alternativas e confirmação indisponíveis

### Requirement: Seleção inédita e ordenada por assunto
Ao criar um caderno, a API SHALL excluir questões com resposta final do mesmo usuário nos últimos 30 dias e questões presentes em cadernos não finalizados do mesmo usuário. A seleção congelada SHALL ser ordenada por assunto canônico de forma determinística.

#### Scenario: Questões recentes ou reservadas
- **WHEN** a pessoa criar um caderno e existirem questões elegíveis já respondidas recentemente ou reservadas em outro caderno aberto
- **THEN** essas questões SHALL ficar fora da nova seleção

#### Scenario: Elegibilidade insuficiente
- **WHEN** as exclusões deixarem menos questões que a quantidade solicitada
- **THEN** a API SHALL responder `422 VALIDATION_FAILED` e não SHALL repetir questões silenciosamente

#### Scenario: Navegação por assunto
- **WHEN** uma pessoa usar Próximo assunto ou Assunto anterior
- **THEN** a execução SHALL navegar entre blocos consecutivos da seleção congelada ordenada por assunto
