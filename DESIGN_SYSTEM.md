# Design System — ConquistaAI

Referência visual: administração de taxonomia em interface clara, azul e de alta densidade de informação, conforme a imagem de produto recebida em 24/09/2026.

## Princípios

- **Clareza estrutural:** navegação, contexto e ação primária permanecem visíveis sem competir entre si.
- **Hierarquia antes de volume:** árvores usam recuo, ícones, seleção e contadores; formulários mostram somente a tarefa da aba ativa.
- **Feedback honesto:** carregamento, vazio, falha e revisão humana são estados de primeira classe.
- **Responsivo por composição:** painéis passam de duas colunas a uma; ações mantêm alvo mínimo de 40 px.

## Fundamentos visuais

| Elemento | Diretriz |
| --- | --- |
| Fundo | Azul muito claro e superfícies brancas. |
| Cor de ação | Azul primário do Quasar para criar, salvar e atualizar. |
| Superfícies | Cartões brancos, borda azul-acinzentada suave, raio de 16–18 px e sombra discreta. |
| Tipografia | Título forte em azul-marinho; eyebrow em caixa alta, 11 px e espaçamento amplo. |
| Espaçamento | Grade base de 8 px; seções em 16–32 px. |
| Estados | Sucesso verde, atenção âmbar, erro vermelho suave e revisão em lilás/azul claro. |

## Padrões de administração

1. Cabeçalho com eyebrow, título, contexto e ação de atualização.
2. Painel de árvore com busca, contador, seleção e menu contextual.
3. Painel de trabalho com abas: criação, edição, aliases e revisão.
4. Formulários usam `QForm`, `QInput`, `QSelect`, validação e ação primária no rodapé.
5. Operações sensíveis, como fusão, permanecem explícitas e exigem motivo.

## Acessibilidade

- Não depender apenas de cor para comunicar estado.
- Exibir rótulos, mensagens seguras e foco visível.
- Preferir componentes Quasar para teclado, contraste e tamanho de toque.
