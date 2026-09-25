# Design System — ConquistaAI

Referência visual aprovada: painéis desktop e jornadas mobile recebidos em 24/09/2026. O sistema cobre Taxonomia, Revisão, Importação, Desempenho, Catálogo, Assistente, Cadernos e os estados de feedback.

## Princípios

- **Foco na tarefa:** uma página evidencia a decisão principal com título, contexto curto e uma ação primária azul.
- **Densidade organizada:** dados administrativos usam cartões, linhas divisórias sutis, busca e abas; não usam blocos visuais pesados.
- **Continuidade entre contextos:** a navegação, as ações, os filtros e os estados mantêm a mesma gramática em todos os módulos.
- **Mobile é uma composição própria:** o drawer passa a sobreposição, tabelas tornam-se cartões/listas e ações importantes ficam fáceis de alcançar.
- **Feedback honesto:** carregamento, vazio, sucesso, atenção e erro são conteúdos de primeira classe e nunca dependem só de cor.

## Fundamentos visuais

| Token | Valor / uso |
| --- | --- |
| Primária | #1469F5; CTAs, seleção, foco, links e dados em destaque. |
| Título | #10275B; títulos e conteúdo de maior hierarquia. |
| Fundo | #F5F9FF; páginas e áreas externas aos cartões. |
| Superfície | #FFFFFF; cartões, drawer e cabeçalho. |
| Borda | #D8E3F3 / #E4EDF9; delimitação leve de campos e seções. |
| Texto secundário | #71819E; descrições, metadados e instruções. |
| Sucesso / atenção / erro | #12A86B, #EC9613, #E74343; sempre acompanhados por ícone ou texto. |
| Raio | 8 px para controles; 16–18 px para cartões; 10 px para painéis internos. |
| Sombra | Suave, azulada e discreta; somente em cartões de elevação real. |

A tipografia é **Inter** (fallback do sistema). Todo título principal de página usa a classe `page-title`: 26 px / 700 / 1.18 no desktop e 22 px / 700 / 1.16 no mobile, em azul-marinho e tracking levemente fechado. Títulos internos não podem competir com ele. Labels e metadados usam 11–13 px; corpo e controles usam 12–14 px.

## Layout e navegação

### Desktop

- Drawer branco de 252 px, marca no topo, itens com ícone de 17 px e seleção azul clara.
- Cabeçalho de 70 px com busca central, atalho visual Ctrl + K, notificações e avatar.
- Conteúdo começa após o cabeçalho, com largura confortável por contexto e espaçamento de 32–42 px.
- Cabeçalho de página contém contexto, título, descrição e ações sem competir com o conteúdo.

### Mobile

- Abaixo de 900 px, o drawer é sobreposto e acionado pelo botão de menu.
- O cabeçalho mantém menu, busca e avatar; rótulos redundantes desaparecem.
- Em até 599 px, páginas usam 16 px laterais, cartões e grids passam para uma coluna e alvos de toque têm no mínimo 40 px.
- Filtros e abas mantêm rolagem ou distribuição legível; tabelas devem apresentar seus dados como itens de lista quando necessário.

## Componentes e padrões

| Componente | Padrão |
| --- | --- |
| Botão primário | QBtn unelevated, azul, texto objetivo, ícone antes do texto quando ajudar a escanear. |
| Botão secundário | QBtn outline ou flat; usado para limpar, cancelar, exportar e ações não destrutivas. |
| Campo | QInput / QSelect outlined, borda azul-acinzentada, rótulo explícito e foco azul. |
| Cartão | QCard flat, borda suave e raio 16–18 px. Agrupa uma única tarefa ou conjunto coeso de dados. |
| Tabs | QTabs densas, indicador azul e ícone quando houver ganho de reconhecimento. |
| Listas e árvores | Busca antes do conteúdo; ícone, seleção clara, contador/badge quando aplicável e menu contextual opcional. |
| Métrica | Valor grande, label curta e variação com seta/cor/texto; métricas em grade responsiva. |
| Upload | Área pontilhada com ícone, instrução e formatos aceitos; opções em painel separado. |
| Estado vazio | Ícone ou marca, título que explica ausência, orientação segura e CTA de criação quando houver ação possível. |

## Páginas de referência

- **Taxonomia:** árvore pesquisável à esquerda e área de trabalho com abas à direita. Em mobile, árvore e formulário preservam seleção e prioridade da ação.
- **Revisar questões:** filtros e contadores de status antes da lista; cada questão mostra metadados, status e ações sem expor detalhes excessivos.
- **Importar:** etapa atual sempre visível, zona de upload e configurações; o botão de validação ocupa largura disponível no mobile.
- **Desempenho:** cards de métricas seguidos de evolução e barras por assunto; não inventar métricas quando a API não as fornecer.
- **Catálogo:** tabs ou níveis de navegação para concursos, cargos, editais, assuntos e tags; itens usam metadados e ações compactas.
- **Estados:** sucesso verde, atenção âmbar e erro vermelho claro usam QBanner/cartão, ícone e mensagem segura.

## Acessibilidade

- Não comunicar estado apenas por cor; usar ícone, label e texto.
- Todo ícone acionável precisa de aria-label e todos os campos, de rótulo explícito.
- Foco deve permanecer visível em campos, abas, árvore e controles de menu.
- Preservar contraste de texto e não reduzir alvos de toque abaixo de 40 px.

## Implementação

- Quasar é obrigatório; preferir seus componentes antes de CSS estrutural próprio.
- Os tokens ficam em apps/web/src/styles/quasar.variables.sass; regras globais pequenas em apps/web/src/styles/app.sass.
- Estilos locais servem apenas ao acabamento específico da página. Fluxos de dados continuam em Page -> composable -> use case -> repository -> Axios -> API.
- Não usar dados simulados para preencher os padrões de tela: loading, erro, vazio, filtros e paginação devem refletir o contrato disponível.


## Verificação visual

- O build valida tipos e empacotamento, mas não valida fidelidade visual. Use Playwright para capturar os breakpoints desktop (1440 × 900) e mobile (390 × 844).
- Os testes autenticados usam a fixture E2E explícita e determinística; ela popula um banco isolado antes de cada execução e não substitui respostas da API na tela.
- Mudanças em shell, cabeçalho de página, caderno, listagens ou formulários exigem comparação das screenshots com as referências aprovadas.

- Execute `./scripts/test-visual-e2e.sh` para recriar o ambiente `concursos-e2e`, aplicar migrations, carregar a fixture e rodar as screenshots sem depender de IDs ou contas manuais.
