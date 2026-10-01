# Design System — ConquistaAI

## Direção: Arena de conquista

A identidade do ConquistaAI é a da Arena: um ambiente de estudo concentrado, azul-noturno e orientado à conquista. A interface privilegia informação útil, ritmo de progresso e contraste preciso — sem simular um painel corporativo claro.

A Arena não é uma exceção visual. Ela define a linguagem de todas as jornadas, da entrada ao estudo, revisão, catálogo e administração.

## Fundamentos

| Papel | Token | Uso |
| --- | --- | --- |
| Canvas | `#071221` | fundo geral e áreas imersivas |
| Superfície | `#0C1A2E` | cards, painéis e áreas de leitura |
| Superfície elevada | `#0E2038` | menus e diálogos |
| Linha | `#1D3453` | limites e divisores discretos |
| Texto | `#E8F2FF` | títulos e conteúdo prioritário |
| Texto auxiliar | `#8CA2C3` | metadados e orientação |
| Azul elétrico | `#1482F6` | ação principal, seleção e foco |
| Azul claro | `#78C4FF` | informação ativa e feedback |
| Menta | `#49D89A` | sucesso e progresso concluído |
| Dourado | `#FFD55D` | conquista, pontuação e marco positivo |
| Coral | `#FF766B` | erro, bloqueio e ação destrutiva |

Tipografia: **Inter**, com fallback de sistema. Títulos são compactos, em alto contraste e com tracking negativo leve. Eyebrows e labels de contexto usam caixa alta, azul-claro e espaçamento amplo.

## Princípios

- **Uma atmosfera, todos os módulos.** Canvas, shell e superfícies seguem a paleta Arena em toda navegação autenticada.
- **Concentração antes de decoração.** Use planos escuros, bordas sutis e espaço para destacar tarefas; sombras só expressam elevação real.
- **Conquista tem significado.** Dourado é exclusivo de placar, conquista e progresso relevante; azul é a ação.
- **Estado é explícito.** Sucesso, alerta e erro combinam cor, ícone e texto. Nunca dependem só de cor.
- **Mobile preserva o ritmo.** A composição reduz densidade e mantém ações alcançáveis, sem comprimir o desktop.

## Shell e superfícies

- Drawer e cabeçalho usam azul-noturno. A navegação ativa aparece em faixa azul-profundo com indicador azul-claro.
- Cards, listas e oficinas usam superfície azul-profundo, borda `#1D3453` e sem sombras decorativas.
- Diálogos e menus usam superfície elevada para se separar da página.
- O canvas pode receber apenas gradiente radial azul discreto; não usar padrões, ilustrações ou brilho repetitivo.

## Componentes Quasar

| Componente | Regra |
| --- | --- |
| CTA primário | `QBtn unelevated` azul-elétrico, texto branco e verbo claro. Um por agrupamento. |
| Ação secundária | `QBtn outline` com borda azul-petróleo e texto azul-claro. |
| Campo | `QInput`/`QSelect` outlined sobre fundo azul-profundo, label legível e foco azul-claro. |
| Tabs | faixa escura, item ativo azul-claro e indicador visível. Em mobile, rolagem horizontal. |
| Lista | superfície contínua com divisores sutis; hover azul-profundo mais claro. |
| Métrica | valor em texto claro; azul para dado ativo, menta para evolução e dourado para conquista. |
| Banner | superfície tonal compatível, ícone e mensagem; erro coral e sucesso menta. |
| Foco | todo controle de teclado recebe anel azul-claro de 2 px. |

## Layout e responsividade

Desktop usa conteúdo proporcional à tarefa, com respiro de 32–42 px e assimetria apenas para priorizar leitura. Em até 899 px, a navegação vira drawer sobreposto. Em até 599 px, páginas usam 16 px laterais, listas e leitura ficam em uma coluna e controles têm alvo mínimo de 40 px.

Questões e conversas são superfícies de foco: texto claro, largura confortável e controles sem ruído. Telas administrativas mantêm oficina e biblioteca, porém com as mesmas superfícies escuras da Arena.

## Acessibilidade

- Texto principal e auxiliar devem conservar contraste adequado sobre suas superfícies.
- Ícones acionáveis têm `aria-label`; campos têm label visível ou acessível.
- A ordem do DOM acompanha a prioridade visual.
- Estados de carregamento, vazio, sucesso, atenção e erro informam significado por texto e/ou ícone além da cor.

## Implementação e verificação

- Tokens Quasar vivem em `apps/web/src/styles/quasar.variables.sass`.
- A fundação transversal vive em `apps/web/src/styles/app.sass`; o shell em `apps/web/src/Interface/Http/Layout/AppShell.vue`.
- Páginas refinam apenas sua composição e preservam o fluxo Page/Component → Composable → Use Case → Repository → Axios → API.
- Verifique desktop em 1440 × 900 e mobile em 390 × 844. Execute `npm run build` no frontend e, quando disponível, `./scripts/test-visual-e2e.sh`.
