## Context

O tema global é escuro, mas diversas páginas possuem estilos com escopo próprio, com cores e fundos do tema claro. Essas regras ganham precedência sobre parte da fundação compartilhada e deixam títulos, inputs, tabelas e tabs sem hierarquia consistente.

## Goals / Non-Goals

**Goals:**

- Estabelecer uma escala legível de texto, superfícies, bordas e seleção em toda a SPA.
- Cobrir componentes Quasar que estruturam formulários, tabelas, tabs, menus e paginação.
- Remover a aparência clara residual sem alterar os fluxos das telas.

**Non-Goals:**

- Alterar conteúdo, layout funcional, contratos de API ou componentes de domínio.
- Substituir Quasar ou introduzir dependências visuais.

## Decisions

- A correção será centralizada em regras globais com seletor e prioridade suficientes para coexistir com estilos locais legados. Isso corrige todas as telas de forma auditável; reescrever cada página multiplicaria risco e inconsistência.
- A escala terá três níveis claros: texto principal, texto secundário e metadado. Fundo de edição, tabela e seleção receberão superfícies próprias, em vez de reutilizar branco ou cinza claro.
- O destaque usa azul-elétrico para interação e dourado somente para progresso/conquista; tabs e cabeçalhos de tabelas usam contraste estrutural, sem efeitos decorativos.

## Risks / Trade-offs

- [Uma regra global pode conflitar com área imersiva] → exceções usam classes semânticas já existentes, e a compilação valida os estilos.
- [Contraste alto demais pode cansar] → texto auxiliar permanece azul-acinzentado e bordas não competem com conteúdo.

## Migration Plan

1. Estender a fundação global para controles e conteúdo tabular.
2. Executar build e inspeções de regras claras residuais.
3. Registrar a validação no estado de desenvolvimento. Não há dados ou APIs a migrar.

## Open Questions

Nenhuma.
