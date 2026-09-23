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

Cada módulo mantém Domain, Application, Infrastructure e Interface separados; páginas Quasar somente coordenam composables e eventos.

## Marca

O nome da aplicação é **ConquistaAI**. O lema oficial é: **“Estude. Evolua. Conquiste.”**. Use ambos nos pontos de marca da interface e preserve o ativo visual em apps/web/public/images/concursos-study-mark.png.

## Alterações recentes de interface

- A página HTML declara `viewport` para evitar a renderização em largura de desktop no celular.
- Em larguras menores que 900 px, o menu é um drawer sobreposto aberto pelo cabeçalho; em desktop ele permanece lateral.
- O Vite aceita somente `conquistaai.app.br` e `www.conquistaai.app.br`, sem liberar hosts genéricos.
- A execução de cadernos mantém controles compactos, cronômetro, pausa e alternativas com áreas de toque adequadas.
