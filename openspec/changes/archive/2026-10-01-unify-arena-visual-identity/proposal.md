## Why

A Arena já oferece uma identidade visual escura, concentrada e orientada à conquista, enquanto o restante da aplicação mantém uma linguagem editorial clara incompatível. Unificar a experiência elimina a quebra de contexto ao navegar e consolida uma marca reconhecível em todas as jornadas.

## What Changes

- Adotar a paleta azul-noturna, superfícies azul-profundo, contornos discretos, ação azul-elétrica e destaque de conquista dourado da Arena como fundação global.
- Aplicar a nova fundação ao shell, páginas, componentes Quasar e estados de carregamento, vazio, erro e foco, mantendo responsividade e acessibilidade.
- Substituir a documentação da identidade visual pelo contrato baseado na Arena.
- Preservar fluxos, conteúdo, contratos HTTP e arquitetura DDD existentes.

## Capacidades

### Novas capacidades

- `arena-visual-identity`: Define a identidade visual global baseada na Arena para as jornadas autenticadas e de entrada.

### Capacidades modificadas

- Nenhuma.

## Impacto

- `apps/web/src/styles/quasar.variables.sass`, `apps/web/src/styles/app.sass` e `apps/web/src/Interface/Http/Layout/AppShell.vue`.
- Páginas Quasar que recebem a fundação visual global, sem alteração de APIs, dependências ou regras de negócio.
- `DESIGN_SYSTEM.md` e `docs/DEVELOPMENT_STATE.md`.
