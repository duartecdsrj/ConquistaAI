## ADDED Requirements

### Requirement: Fundação visual unificada
A SPA autenticada MUST apresentar a identidade da Arena como fundação visual, com canvas azul-noturno, superfícies azul-profundo, contornos de baixo contraste, texto claro e ação primária azul-elétrica.

#### Scenario: Navegação entre módulos
- **WHEN** uma pessoa autenticada alterna entre Arena e qualquer módulo da navegação lateral
- **THEN** cabeçalho, drawer, canvas e superfícies mantêm a mesma família visual escura sem uma transição para a identidade clara anterior

### Requirement: Semântica visual e acessibilidade
A fundação MUST reservar dourado para conquista ou progresso, manter cores semânticas distintas para sucesso, atenção e erro, e expor foco de teclado perceptível sem depender apenas de cor.

#### Scenario: Estado interativo focado
- **WHEN** uma pessoa navega por botão, campo, tab ou item de lista usando teclado
- **THEN** o elemento focado apresenta um indicador visual com contraste suficiente e seu estado permanece compreensível por texto ou ícone quando aplicável

### Requirement: Responsividade preservada
A identidade MUST conservar a composição responsiva: drawer sobreposto em telas abaixo de 900 px, espaçamento lateral de 16 px em telas até 599 px e controles com alvo mínimo de 40 px.

#### Scenario: Uso em tela móvel
- **WHEN** a SPA é exibida em viewport de 390 × 844 px
- **THEN** navegação, formulários, listas e ações permanecem legíveis, alcançáveis e visualmente coerentes com a identidade da Arena
