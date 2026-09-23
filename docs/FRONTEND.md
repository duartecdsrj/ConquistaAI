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

## Quasar e identidade visual

Quasar e a base de componentes da interface. O plugin Vite e configurado em apps/web/vite.config.ts e o bootstrap da aplicacao importa o CSS do framework em main.ts.

As cores da identidade visual ficam em src/styles/quasar.variables.sass. Estilos globais estritamente necessarios ficam em src/styles/app.sass. Componentes devem preferir QLayout, QPage, QCard, QForm, QInput, QBtn, QBanner e QAvatar, aplicando classes locais apenas para acabamento visual.

Nao importar outro framework CSS, nem substituir a paleta azul clara, os fundos suaves, os cartoes com cantos arredondados ou a tipografia atual sem uma decisao de produto.
