## Why

A fundação escura foi aplicada, mas regras locais de páginas ainda exibem texto, superfícies e controles com contraste de tema claro. Isso compromete hierarquia e leitura em campos, tabelas e tabs.

## What Changes

- Normalizar a escala de texto e superfícies escuras em todas as páginas e componentes compartilhados.
- Definir aparência explícita para campos, tabelas, tabs, menus, estados e conteúdo de leitura.
- Eliminar fundos claros residuais e garantir destaque legível para títulos, cabeçalhos e seleção.
- Preservar conteúdo, fluxos, APIs e arquitetura existentes.

## Capabilities

### New Capabilities

- Nenhuma.

### Modified Capabilities

- `arena-visual-identity`: amplia a fundação visual para garantir contraste e hierarquia em controles e conteúdos de todas as telas.

## Impact

- `apps/web/src/styles/app.sass` e estilos locais que mantiverem regras claras conflitantes.
- Sem alterações de API, dependências ou regras de negócio.
