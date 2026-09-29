# question-learning-interactions Specification

## Purpose
TBD - created by archiving change question-learning-interactions. Update Purpose after archive.
## Requirements
### Requirement: Marcações pessoais independentes
O sistema SHALL permitir que cada usuário ative ou desative favorito, revisar depois e não dominei de forma independente para uma questão, sem alterar o resultado de uma tentativa.

#### Scenario: Acerto com não domínio
- **WHEN** o usuário acertar uma questão e ativar não dominei
- **THEN** o resultado da tentativa permanece correto e a marcação não dominei fica ativa para aquele usuário e questão

#### Scenario: Combinação de marcações
- **WHEN** o usuário ativar favorito, revisar depois e não dominei
- **THEN** as três marcações são persistidas e retornadas simultaneamente

### Requirement: Anotação privada da questão
O sistema SHALL permitir uma anotação privada editável por usuário e questão, com data de criação e última atualização.

#### Scenario: Edição de anotação própria
- **WHEN** o autor salvar novo conteúdo para uma anotação existente
- **THEN** o conteúdo e a data de atualização são alterados sem criar uma segunda anotação

#### Scenario: Isolamento da anotação
- **WHEN** outro usuário consultar a mesma questão
- **THEN** a anotação privada do autor não é retornada nem alterável

### Requirement: Comentários e relatos de problema
O sistema SHALL aceitar comentários públicos com referência opcional a comentário pai e relatórios autenticados por categoria, descrição e estado.

#### Scenario: Resposta a comentário
- **WHEN** um usuário publicar comentário com comentário pai da mesma questão
- **THEN** a resposta é persistida como encadeada àquele comentário

#### Scenario: Relato categorizado
- **WHEN** um usuário reportar imagem ausente com descrição
- **THEN** o relatório é persistido com usuário, questão, categoria, descrição, data e estado inicial

### Requirement: Barra de ações responsiva
O sistema SHALL exibir ações de interação nas telas de resolução e visualização completa de questão, usando componentes Quasar e dados reais.

#### Scenario: Ação reversível em tela pequena
- **WHEN** o usuário tocar favorito, revisar depois ou não dominei em tela pequena
- **THEN** a barra atualiza imediatamente e agrupa as ações secundárias sem remover as três ações principais

#### Scenario: Falha na atualização otimista
- **WHEN** a API rejeitar uma alteração de marcação
- **THEN** a interface restaura o estado confirmado e apresenta erro seguro

### Requirement: Filtros combináveis de interação
O sistema SHALL aceitar filtros combináveis de resolução e interação para questões do usuário autenticado.

#### Scenario: Filtro de erro e insegurança
- **WHEN** o usuário filtrar questões erradas, não dominadas e por assunto canônico
- **THEN** a listagem contém somente questões que atendem a todos os filtros e retorna paginação padrão
