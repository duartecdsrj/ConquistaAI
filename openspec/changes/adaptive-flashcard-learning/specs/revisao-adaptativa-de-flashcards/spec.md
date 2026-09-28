## ADDED Requirements

### Requirement: Estado individual e histórico imutável de revisão
O sistema SHALL manter o conteúdo do flashcard separado do progresso por usuário e SHALL registrar cada classificação de revisão como evento imutável com estado anterior e posterior.

#### Scenario: Usuário classifica um card como difícil
- **WHEN** um usuário autenticado envia a classificação `HARD` para um card da sua sessão
- **THEN** o sistema registra uma revisão imutável e atualiza somente o progresso daquele usuário

### Requirement: Repetição espaçada substituível
O sistema SHALL calcular a próxima revisão por uma estratégia de domínio configurável, com intervalos iniciais centralizados para `AGAIN`, `HARD`, `GOOD` e `EASY`.

#### Scenario: Não lembrei
- **WHEN** a classificação for `AGAIN`
- **THEN** a próxima revisão é antecipada para o intervalo curto configurado e o domínio não aumenta

### Requirement: Fila priorizada de revisão
O sistema SHALL selecionar a fila pelo cálculo central de prioridade, considerando no mínimo vencimento, lacuna de domínio e sinais recentes de questões, e não por ordem aleatória.

#### Scenario: Revisão rápida
- **WHEN** o usuário solicita revisão rápida
- **THEN** o sistema devolve uma quantidade limitada dos cards de maior prioridade disponíveis

### Requirement: Deduplicação de conteúdo
O sistema SHALL impedir a criação de flashcard semanticamente equivalente para o mesmo conceito e tipo por fingerprint normalizada versionada.

#### Scenario: Card equivalente já existe
- **WHEN** uma ação validada solicitar conteúdo com fingerprint já persistida
- **THEN** o sistema reutiliza o card existente sem criar outro registro
