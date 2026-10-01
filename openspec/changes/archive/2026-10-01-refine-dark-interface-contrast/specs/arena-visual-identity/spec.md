## MODIFIED Requirements

### Requirement: Fundação visual unificada
A SPA autenticada MUST apresentar a identidade da Arena como fundação visual, com canvas azul-noturno, superfícies azul-profundo, contornos de baixo contraste, texto claro e ação primária azul-elétrica. Todas as páginas MUST aplicar hierarquia de texto legível e não podem exibir fundo, título, campo, tabela, tab, menu ou paginação no padrão claro residual.

#### Scenario: Navegação entre módulos
- **WHEN** uma pessoa autenticada alterna entre Arena e qualquer módulo da navegação lateral
- **THEN** cabeçalho, drawer, canvas, superfícies, conteúdo de formulário e navegação tabular mantêm a mesma família visual escura sem uma transição para a identidade clara anterior

#### Scenario: Conteúdo estruturado em página administrativa
- **WHEN** uma pessoa abre uma tela com campos, tabs ou tabela
- **THEN** rótulos, valores, cabeçalhos, linhas, seleção e paginação têm contraste e superfícies coerentes com a identidade Arena
