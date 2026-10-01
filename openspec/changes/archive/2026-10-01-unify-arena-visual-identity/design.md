## Context

As páginas gerais usam tokens claros e a Arena possui uma composição escura própria, inclusive um modificador no shell. A mudança é transversal ao frontend, não altera dados nem contratos, e precisa conservar Quasar, responsividade, acessibilidade e os limites DDD descritos em `docs/FRONTEND.md`.

## Goals / Non-Goals

**Goals:**

- Fazer da linguagem visual compacta da Arena a fundação de toda a SPA.
- Centralizar tokens e padrões compartilhados para que páginas existentes recebam a nova identidade sem duplicar estilos.
- Manter contraste, foco visível e estados semânticos em desktop e mobile.

**Non-Goals:**

- Alterar jornadas, conteúdo, composição de dados, contratos HTTP ou lógica de apresentação.
- Reescrever páginas individuais cuja composição já é compatível com os padrões globais.
- Mudar a interface específica de duelo além de fazê-la herdar os mesmos tokens.

## Decisions

- Os tokens de `quasar.variables.sass` e as variáveis CSS globais usarão azul-noturno como canvas e azul-profundo como superfície. Essa é a fonte única da paleta; editar cada página isoladamente manteria divergências.
- `app.sass` aplicará a fundação a componentes Quasar e a classes de layout compartilhadas com seletores de baixa complexidade e prioridades explícitas apenas onde forem necessárias para superar estilos locais legados. Isso preserva componentes e fluxos sem uma reescrita de todos os templates.
- O `AppShell` deixará de usar a exceção `arena-shell`: drawer e cabeçalho serão escuros em toda navegação autenticada. A alternativa de manter o shell claro prolongaria a transição visual que a mudança pretende remover.
- Azul elétrico será a ação principal, dourado será reservado a conquista/progresso e estados semânticos manterão cores distintas acompanhadas de texto ou ícone. A alternativa de usar a cor primária para todos os destaques reduziria a leitura de estados.

## Risks / Trade-offs

- [Estilos locais claros podem manter contraste insuficiente] → regras globais cobrem superfícies, textos, campos e banners; o build e a inspeção de seletores identificam exceções.
- [Sobrescritas globais podem afetar diálogos] → diálogos recebem superfície e contorno próprios, sem herdar transparências de cards de página.
- [Tema escuro pode reduzir legibilidade] → tokens separam texto principal, auxiliar e borda e preservam anel de foco azul-claro.

## Migration Plan

1. Atualizar tokens, estilos globais e shell.
2. Atualizar o documento de design e o estado de desenvolvimento.
3. Executar o build da SPA e inspeções estáticas de estilos.
4. A reversão consiste em restaurar os três arquivos de estilo e o shell; não há migração de dados nem API.

## Open Questions

Nenhuma.
