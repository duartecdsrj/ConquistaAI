# Design System — ConquistaAI

## Direção: caderno editorial de preparação

A identidade do ConquistaAI não deve parecer um painel corporativo. Ela combina a precisão de uma ferramenta de estudo com a materialidade de um caderno bem editado: páginas claras, tinta azul-marinho, blocos de conteúdo com propósito e sinais de progresso que convidam a continuar.

A aplicação preserva o comportamento e os componentes Quasar existentes. A mudança é de hierarquia e composição: cartões não são a unidade padrão da página; são usados apenas quando criam foco, contenção ou uma decisão clara.

## Princípios

- **Uma página, uma intenção.** Cada tela começa por contexto, título e uma frase de orientação. A ação principal aparece perto dessa narrativa, não perdida em uma barra de controles.
- **Camadas em vez de caixas.** Fundo, faixa de contexto, superfície de leitura e notas laterais são camadas distintas. Não cercar cada fragmento de informação com uma borda.
- **Aprender tem ritmo.** Métricas ficam em faixas, listas se comportam como uma biblioteca e a questão é tratada como papel de leitura, não como linha de tabela.
- **Cor com semântica, não decoração.** Azul conduz a ação; violeta indica conexão/conhecimento; menta comunica avanço; âmbar chama atenção. Cores de apoio não substituem texto, ícone ou estado.
- **Mobile é outra composição.** Não é o desktop comprimido: a narrativa vem antes, ações são alcançáveis, tabs rolam horizontalmente e a densidade diminui.

## Fundamentos visuais

| Papel | Token | Uso |
| --- | --- | --- |
| Tinta | #132750 | títulos, navegação e texto de maior contraste |
| Azul de ação | #1769F6 | CTA, foco, seleção e dados principais |
| Violeta de conhecimento | #7A62EC | conexões, trilhas, contexto de taxonomia e acentos editoriais |
| Menta de progresso | #44C59A | sucesso, evolução e conclusão |
| Âmbar de atenção | #FFBD45 | alerta e estado que pede leitura |
| Coral de erro | #FF766B | erro destrutivo, sempre com mensagem |
| Papel | #F8FBFF | superfícies de leitura e painéis principais |
| Canvas | #EDF4FF | página, com gradientes atmosféricos discretos |
| Linha | #DCE7F6 | divisores e contornos necessários |
| Texto auxiliar | #66799B | metadados, instruções e descrições |

Tipografia: **Inter**, fallback de sistema. O título da página é 32–36 px / 750 no desktop e 28–30 px / 750 no mobile, com tracking fechado. O eyebrow é 10–11 px, em caixa alta e espaçamento amplo. Corpo e controles ficam entre 13–15 px; metadados, entre 11–12 px. Não usar mais de três níveis de peso em uma área de leitura.

Os raios são expressivos, mas têm função: 10–13 px em controles, 18–22 px em superfícies de tarefa e 24–28 px em espaços de foco. Sombras são azuladas e só indicam elevação real; o divisor é preferível quando a relação é sequencial.

## Estrutura de página

### Shell

- O drawer é uma estante de estudo: fundo claro com profundidade sutil, marca bem definida e item ativo como faixa azul–violeta, não apenas texto colorido.
- O cabeçalho é translúcido e leve. Não deve competir com o título da página.
- O canvas possui gradientes amplos e quase imperceptíveis; não adicionar ilustrações decorativas repetidas.

### Cabeçalho editorial

- Use eyebrow, título, descrição curta e, quando houver, uma única ação dominante.
- O título recebe sublinhado azul–violeta discreto; esta marca deve aparecer uma vez por página.
- Em páginas de ação contínua (Caderno, Assistente), o cabeçalho pode ser uma faixa de contexto em vez de um bloco separado.

### Superfícies

| Padrão | Quando usar | Característica |
| --- | --- | --- |
| Faixa de panorama | início, desempenho, meta | dados ou narrativa em linha; não quatro cartões soltos |
| Papel de leitura | questão, conversa, explicação | fundo claro, largura confortável e pouco ruído visual |
| Oficina | importação, taxonomia, catálogo | ferramentas agrupadas com contexto e área de trabalho clara |
| Biblioteca | cadernos, recursos, listas | itens com respiro, progressão e ações contextuais |
| Nota | evidência, recomendação, alerta | faixa de cor suave com borda lateral, jamais CTA concorrente |

## Padrões por jornada

- **Início:** abre com uma faixa de propósito. Métricas são uma régua de progresso e os cadernos recentes aparecem como peças de biblioteca com variação sutil de cor.
- **Cadernos e plano:** a meta semanal é uma faixa imersiva azul; prioridades e planos são capítulos subsequentes. A grade de cadernos não deve repetir cartões neutros idênticos.
- **Execução do caderno:** a questão é o centro. Navegador e anotações funcionam como instrumentos laterais; controles de resposta devem ter contraste e área de toque generosa.
- **Desempenho:** indicadores ocupam uma única faixa divisível. O gráfico e os assuntos privilegiam comparação e leitura, não decoração.
- **Banco e revisão:** filtros ficam em uma faixa. Cada questão aberta torna-se papel editorial: cabeçalho com proveniência, enunciado legível, alternativas separadas e ativos preservados.
- **Taxonomia:** árvore é uma biblioteca de conhecimento; área de trabalho é uma oficina. A relação pai–filho precisa ser visível sem poluir com linhas excessivas.
- **Catálogo:** tabs definem o nível de contexto e a tabela/lista mostra apenas o recorte atual. Marcas têm bloco próprio, sem reduzir legibilidade do nome.
- **Importação:** fila e histórico representam um fluxo. O envio é a primeira etapa visual e o histórico fica recolhido até ser necessário.
- **Assistente:** conversa é espaço de leitura; evidências devem parecer notas de rodapé rastreáveis.
- **Login:** é uma abertura de estudo, com composição assimétrica e atmosfera azul/violeta; o formulário continua simples e direto.

## Componentes

| Componente | Regra |
| --- | --- |
| CTA primário | QBtn unelevated, azul. Um por agrupamento visual. Rótulo usa verbo claro. |
| Ação secundária | QBtn flat ou outline; não competir cromaticamente com CTA. |
| Campo | QInput/QSelect outlined, label explícito, fundo quase branco e foco azul. |
| Tabs | densas, em faixa; item ativo tem indicador e superfície suave. Em mobile, usar rolagem horizontal. |
| Métrica | label curta, número ou valor com contraste, descrição contextual. Agrupar em régua ou faixa antes de criar cards. |
| Lista | divisores leves ou linhas pontilhadas; estados de hover/seleção devem ser visíveis. |
| Estado vazio | mensagem específica, orientação e CTA quando existir próxima ação segura. Não criar ilustração genérica sem utilidade. |
| Banner/nota | ícone + mensagem; sucesso, atenção e erro nunca dependem somente de cor. |

## Desktop e mobile

No desktop, o conteúdo tem largura definida pelo tipo de tarefa, com respiro de 32–42 px. Use assimetria quando ela destacar foco: leitura maior, ferramentas menores. Grids de quatro colunas são reservados a uma única faixa de indicadores.

No mobile (até 600 px), usar 16 px laterais, título menor e blocos de 20–22 px de raio. Faixas podem avançar 4 px além da grade para criar presença. Métricas podem formar matriz 2 × 2; listas e superfícies de leitura ficam em uma coluna. Nenhum controle deve ter alvo inferior a 40 px.

## Acessibilidade

- Estado não é apenas cor: use texto, ícone e contraste.
- Todo ícone acionável tem aria-label; campo possui label visível ou acessível.
- Foco de teclado permanece visível em botões, tabs, árvores, itens de lista e menus.
- A ordem de leitura no DOM acompanha a prioridade visual, especialmente no mobile.
- Não colocar texto auxiliar com contraste insuficiente sobre gradiente ou cor de apoio.

## Implementação

- Quasar é obrigatório. Os tokens vivem em apps/web/src/styles/quasar.variables.sass; a linguagem transversal vive em apps/web/src/styles/app.sass.
- Estilos locais só refinam a composição da página. Eles não alteram dados, regras de negócio ou o fluxo Page → composable → use case → repository → Axios → API.
- Reutilizar os padrões deste documento antes de criar novo cartão, sombra ou tonalidade.
- Não usar conteúdo demonstrativo: loading, erro, vazio, filtros e paginação refletem o contrato real da API.

## Verificação visual

- Build verifica tipos e empacotamento; Playwright verifica a composição em desktop (1440 × 900) e mobile (390 × 844).
- Execute ./scripts/test-visual-e2e.sh para levantar o ambiente isolado concursos-e2e, aplicar migrations, carregar a fixture determinística e registrar os cenários sem IDs ou contas manuais.
- Mudanças em shell, cabeçalho, caderno, listas, filtros ou formulários exigem screenshots novas e comparação consciente. Baseline é uma referência de regressão, não substituto de revisão humana.
