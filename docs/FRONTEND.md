# Frontend

## Arquitetura obrigatoria

O frontend segue DDD adaptado a SPA e organiza cada contexto em quatro camadas:

Interface (pages/components/composables) -> Application (use cases) -> Domain (models and ports) <- Infrastructure (Axios repositories)

- Domain/<Context> contem modelos, tipos imutaveis e interfaces de repositorio. Nao importa Vue, Quasar, Axios ou navegador.
- Application/<Context> contem casos de uso. Recebe comandos e usa somente contratos de dominio.
- Infrastructure contem Axios, armazenamento do navegador e implementacoes dos repositorios.
- Interface/Http contem paginas, componentes e composables. Telas nao chamam Axios, nao acessam localStorage e nao possuem regra de negocio.
- Infrastructure/Container.ts e o ponto de composicao das dependencias.

O fluxo de uma acao deve ser:

Page -> composable/view model -> use case -> repository interface -> Axios repository -> API
API -> envelope DTO -> mapper/repository -> use case -> composable -> page

## Cliente HTTP e contratos

Somente Infrastructure/Http/AxiosApiClient.ts consome Axios diretamente. Ele padroniza:

- base URL /api/v1, Accept application/json e withCredentials;
- injecao do token Bearer por interceptor;
- conversao do envelope data/meta em retorno tipado;
- conversao de falhas em ApiRequestError, com codigo, status, detalhes e request id;
- paginacao com PageQuery, PageResult e paginationParams.

Novos repositorios devem usar getData, postData ou getPage. Nunca retornar AxiosResponse, arrays sem tipo ou o envelope HTTP para a camada de aplicacao.

Listagens usam page e per_page, com padrao 1 e 25 e maximo 100. Filtros devem ser DTOs imutaveis do contexto e ser serializados pelo repositorio de infraestrutura.

## Identity

O modulo Identity possui:

- contrato AuthRepository e porta SessionStore no dominio;
- casos de uso de login, restauracao e logout na aplicacao;
- AxiosAuthRepository e BrowserSessionStore na infraestrutura;
- useAuth como adaptador de apresentacao.

A tela de login apenas coleta os valores e emite um evento. A validacao de credenciais e a persistencia de sessao pertencem aos casos de uso e adaptadores.

## Execucao e validacao

docker compose up -d --build
docker compose exec frontend npm install
docker compose exec frontend npm run build

A interface fica disponivel em http://localhost:8081.

## Responsividade

A interface é mobile-first: em telas menores que 900 px, a navegação lateral vira drawer sobreposto acionado pelo cabeçalho; em até 599 px, páginas usam espaçamento de 16 px, grades passam a uma coluna e ações mantêm alvo mínimo de 40 px. A resolução de cadernos compacta os controles do cabeçalho, mantém cronômetro e pausa acessíveis e preserva alternativas com área de toque mínima de 54 px.

## Quasar e identidade visual

Quasar e a base de componentes da interface. O plugin Vite e configurado em apps/web/vite.config.ts e o bootstrap da aplicacao importa o CSS do framework em main.ts.

As cores da identidade visual ficam em src/styles/quasar.variables.sass. Estilos globais estritamente necessarios ficam em src/styles/app.sass. Componentes devem preferir QLayout, QPage, QCard, QForm, QInput, QBtn, QBanner e QAvatar, aplicando classes locais apenas para acabamento visual.

Nao importar outro framework CSS, nem substituir a paleta azul clara, os fundos suaves, os cartoes com cantos arredondados ou a tipografia atual sem uma decisao de produto.

## Entrega paralela de backend e frontend

A partir da Fase 1, uma funcionalidade só é considerada entregue quando sua experiência correspondente também está disponível na SPA, sempre que houver interação humana aplicável.

- Antes de iniciar o próximo item do roadmap, conclua no mesmo ciclo o contrato HTTP, o caso de uso backend, o repositório Axios, o caso de uso frontend, o composable e a tela ou componente Quasar.
- A prioridade de interface acompanha a prioridade de produto: autenticação, cadernos/resolução, questões, desempenho; depois catálogo e importação administrativa.
- Recursos exclusivamente internos (migrações, observabilidade, segurança de infraestrutura) não exigem tela, mas devem ser documentados.
- Não use dados simulados para representar recursos já atendidos pela API. A tela deve consumir o contrato real, inclusive estados de carregamento, erro, vazio, filtros e paginação quando aplicável.
- Toda nova rota ou mudança de contrato exige atualização de docs/API.md; todo novo módulo visual exige atualização deste arquivo.

A marca oficial do produto está em apps/web/public/images/concursos-study-mark.png. Ela deve ser reutilizada como imagem — sem recriação em CSS, SVG ou texto — nos pontos de marcação da experiência.

## Módulos já conectados à API

- Study: lista, criação, início, execução em tela inteira, finalização e navegação pela seleção congelada. A tela de execução apresenta apenas uma questão por vez, cronômetro persistido com pausa/retomada, estatísticas reais do caderno, restauração de respostas já registradas, ações Anterior/Próxima e confirmação de finalização.
- Question Bank: consulta de questões publicadas com filtros.
- Performance: métricas básicas do usuário.
- Catalog: administração em cascata de concursos, cargos, editais, assuntos e tags para usuários ADMIN.
- Editorial: listagem de rascunhos e publicação administrativa de questões validadas.
- Taxonomy: árvore de assuntos canônicos e criação de nós administrativos; a tela consome a lista paginada da API e não contém regras de negócio.
  A criação de aliases seleciona o assunto canônico e envia apenas o comando tipado ao caso de uso; a normalização e a prevenção de duplicidade permanecem no backend.
  A edição seleciona um assunto existente e envia o comando PATCH pelo caso de uso; a API bloqueia a troca de pai quando o assunto ainda possui filhos.

Cada módulo mantém Domain, Application, Infrastructure e Interface separados; páginas Quasar somente coordenam composables e eventos.

## Marca

O nome da aplicação é **ConquistaAI**. O lema oficial é: **“Estude. Evolua. Conquiste.”**. Use ambos nos pontos de marca da interface e preserve o ativo visual em apps/web/public/images/concursos-study-mark.png.

## Alterações recentes de interface

- A página HTML declara `viewport` para evitar a renderização em largura de desktop no celular.
- Em larguras menores que 900 px, o menu é um drawer sobreposto aberto pelo cabeçalho; em desktop ele permanece lateral.
- O Vite aceita somente `conquistaai.app.br` e `www.conquistaai.app.br`, sem liberar hosts genéricos.
- A execução de cadernos mantém controles compactos, cronômetro, pausa e alternativas com áreas de toque adequadas.

## Proveniência de assuntos do edital

O módulo Catalog permite ao administrador informar, ao criar um assunto, a página e o trecho correspondente do PDF. A página `CatalogPage.vue` encaminha o evento ao caso de uso `CatalogUseCases.createSubject`; o repositório Axios é o único responsável por serializar `source_excerpt` e `source_page` para a API. A tela nunca chama Axios diretamente.

## Associação canônica no catálogo

A tela administrativa de catálogo carrega assuntos canônicos ativos pelo caso de uso de Taxonomy e permite ao administrador associá-los explicitamente ao assunto local selecionado. O comando passa por `CatalogUseCases.assignTaxonomySubjects` e `AxiosCatalogRepository`; a página não consome HTTP diretamente. A associação não sugere nem efetiva fusões.

## Processamento de editais

O módulo Catalog oferece revisão administrativa do processamento de PDF: o administrador enfileira ou reprocessa explicitamente um edital, consulta o progresso persistido e expande as páginas extraídas com seus offsets. A página usa somente `useCatalog`; jobs e extrações passam por `CatalogUseCases` e `AxiosCatalogRepository`.

## Importação e revisão de duplicidades

A tela Import usa `useImport` como adaptador de interface. Ela carrega concurso, cargo e edital pelo composable, apresenta as linhas bloqueadas como revisão necessária e destaca candidatos `DUPLICATE_CANDIDATE`; somente linhas válidas podem ser confirmadas como rascunhos.

## Dashboard de desempenho por edital

O módulo Performance consulta `GET /dashboard/me` pelas camadas Domain, Application, Infrastructure e `usePerformance`. A tela Quasar permite selecionar um edital com tentativas concluídas, lista métricas por assunto canônico incluindo descendentes e indica explicitamente quando a amostra ainda não alcançou 10 respostas em 3 dias distintos.

## Design system administrativo e Taxonomia

`DESIGN_SYSTEM.md` registra os fundamentos e padrões administrativos derivados da referência visual aprovada: superfícies claras, hierarquia azul, cartões arredondados, árvore pesquisável e área de trabalho com abas. A tela `TaxonomyPage.vue` aplica esses padrões com os fluxos reais de criação, edição, aliases, sugestões e confirmação de fusão; não introduz chamadas HTTP fora de `useTaxonomy`.

## Plano inteligente de estudos

O módulo Study exibe o plano real de `GET /study-plan/me` e a meta de `GET /study-goals/me` pelo fluxo Domain, Application, Infrastructure e `useNotebooks`. Cada prioridade explica a evidência que a originou e pode apenas preencher a criação de um caderno; a seleção congelada só é criada após confirmação do usuário. A meta semanal é atualizada por `PUT /study-goals/me`, sem HTTP direto na página.

## Assistente com evidências de edital

O módulo Assistant usa `AssistantPage -> useAssistant -> UseCase -> Repository -> Axios -> API`. A conversa permite escolher um edital já extraído e exibe a resposta com páginas de evidência; a página não envia contexto, acessa Axios nem persiste transcrições diretamente.

## Descoberta web controlada

O módulo Discovery é administrativo e segue `DiscoveryPage -> useDiscovery -> UseCase -> Repository -> Axios -> API`. A tela envia somente consulta e tipo, exibe candidatos e respectivas URLs/proveniência; não realiza navegação HTTP direta nem baixa documentos.


## Catálogo: cadastro de concurso com edital

A aba **Concursos** usa `POST /admin/exams/with-notice` por meio de `CatalogUseCases.createExamWithNotice`. A page não monta FormData nem chama HTTP: ela envia os dados ao composable, que usa o caso de uso e o repositório Axios. O PDF é opcional, mas, quando fornecido, o backend cria o edital principal e agenda sua extração automaticamente.


## Logos no catálogo

O tipo `Exam` contém URLs de logo da instituição e da organizadora devolvidas pela API. `CatalogPage` as apresenta em blocos de marca quadrados, com o concurso e a banca em hierarquia própria; o cadastro não possui campo manual de imagem e a ausência de URL conserva o ícone padrão.

## Catálogo contextual

A tela apresenta os concursos na primeira aba. Na aba Cargos, a ação **Assuntos** abre uma seleção múltipla de assuntos canônicos: ela persiste a matriz exclusiva do cargo e é a fonte usada no estudo direcionado. Ao abrir Cargos ou Editais, o administrador seleciona antes o concurso; as listagens e criações ficam limitadas a esse escopo. A aba Assuntos requer adicionalmente a escolha de um edital. Tags são globais. Ações de visualizar levam ao contexto do concurso e editar usa `CatalogUseCases.updateExam`; o envio posterior de PDF apenas anexa o arquivo e não enfileira processamento.


## Histórico de PDFs importados

A tela Import restaura os jobs persistidos ao ser aberta. Itens pendentes ou em processamento ficam visíveis e atualizam por polling; itens concluídos ou com falha permanecem no histórico recolhido, aberto sob demanda. A listagem é fornecida por `ImportUseCases` e `AxiosImportRepository`, sem estado local como fonte de verdade.


## Conteúdo rico de questões

A revisão editorial e a execução de caderno reutilizam `QuestionContent.vue` para apresentar texto, tabelas Markdown e blocos de código, e `QuestionAssetImage.vue` para imagens extraídas do PDF. Este último obtém o binário com Axios autenticado e expõe somente uma URL Blob temporária ao componente de imagem; não há rota pública de arquivos.


## Questões de correlação

`QuestionContent.vue`, compartilhado pela revisão editorial e pelo caderno, reconhece enunciados que contenham itens numerados e afirmações com `( )`. Esses conteúdos são apresentados como quadro de duas colunas: itens numerados à esquerda e afirmações a relacionar à direita. Em telas com até 599 px, o quadro passa para uma coluna, preservando a ordem e a legibilidade.


## Estudo direcionado

A criação de caderno/simulado exige a seleção de concurso e cargo pretendido. Após selecionar o cargo, o formulário busca e permite selecionar somente os assuntos canônicos associados a ele; a página não acessa HTTP diretamente. `NotebooksPage` consulta concursos e cargos pelos casos de uso de Catálogo através do composable, sem HTTP na tela, e envia os IDs ao comando de criação.


## Métricas por concurso

A página Seu desempenho permite escolher um concurso e consulta `dashboard/me` com `exam_id`. Os indicadores e a lista de assuntos consideram apenas tentativas cujas questões pertencem à matriz canônica daquele concurso.


### Importação estruturada

A seção JSON/CSV de Importar questões exige Concurso de referência e Edital de referência, ambos carregados pelo módulo Catalog. O cliente só confirma linhas validadas depois de selecionar um edital real; não há IDs fixos ou contexto implícito no componente.


### Consistência de conteúdo

A tela pública de Questões reutiliza `QuestionContent` e `QuestionAssetImage`, como Revisão e Caderno: tabelas, código e imagens de enunciados ou alternativas recebem a mesma apresentação autenticada. O Assistente exibe provider e modelo da última mensagem retornada pela API, sem rótulo fixo.


### Controles disponíveis

O cabeçalho não exibe busca global nem notificações enquanto não houver casos de uso e contratos de API correspondentes. Isso evita botões sem efeito; navegação, perfil e encerramento de sessão permanecem funcionais.


O seletor administrativo de assuntos por cargo percorre todas as páginas da taxonomia antes de montar suas opções, preservando a árvore e sem ocultar itens após o limite de página da API.


A lista pública de Questões preserva filtros e paginação retornada pela API. O total exibido não representa apenas a primeira página: `q-pagination` solicita explicitamente cada página ao caso de uso.
