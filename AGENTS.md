# Instrucoes obrigatorias para IA e contribuidores

Antes de alterar codigo, leia `docs/ARCHITECTURE.md`, `docs/API.md` e este arquivo. Estas regras prevalecem sobre conveniencias locais.

## Arquitetura obrigatoria

Todo caso de uso deve obedecer exatamente ao fluxo abaixo:

```text
HTTP -> Request DTO -> Controller -> Service -> Repository Interface -> Doctrine Repository
                                                                      |
HTTP <- Response DTO <- Mapper <-------------------------------- Entity
```

### Controller

- Deve ser fino e ter uma responsabilidade HTTP por endpoint.
- Recebe Request DTO ja validado, chama o service e devolve Response DTO usando `ApiResponseFactory`.
- Nao pode conter regra de negocio, query, SQL, DQL, acesso a `EntityManagerInterface`, repository ou entidade Doctrine.
- Nao pode montar arrays de resposta de dominio manualmente.

### DTOs

- Todo request HTTP deve ser convertido em um Request DTO `final readonly` e validado antes de chamar o controller.
- Todo retorno de service que alcance HTTP deve ser um Response DTO `final readonly`.
- Entidades Doctrine nunca podem ser expostas na API.
- DTOs devem ter tipos explicitos; arrays sem forma definida nao podem atravessar camadas.

### Services

- Um service implementa um caso de uso e concentra a regra de negocio e a transacao.
- Recebe e devolve somente DTOs, value objects ou tipos primitivos derivados de DTOs.
- Acessa dados exclusivamente por interfaces de repository do dominio.
- Nao recebe `ServerRequestInterface`, `ResponseInterface`, `Request`, payload HTTP, entidade exposta pelo controller ou conexao Doctrine/PDO.

### Repositories

- Interfaces ficam em `src/Domain/<Context>/Repository`.
- Implementacoes ficam em `src/Infrastructure/Persistence/Doctrine/<Context>`.
- Todas as implementacoes de producao usam Doctrine ORM, `EntityManagerInterface` e `QueryBuilder`/DQL parametrizado.
- E proibido usar PDO, `mysqli`, SQL cru ou arrays de linhas como contrato de persistencia.
- Repositories retornam entidades, colecoes tipadas, value objects ou objetos de leitura definidos pelo dominio; nunca DTOs HTTP.

### Mappers

- Sao responsaveis por converter entidades e resultados de dominio em Response DTOs.
- Podem converter Request DTOs em value objects/comandos de dominio.
- Nao consultam banco, nao chamam services e nao alteram estado.

## Estrutura por contexto

```text
src/
├── Domain/<Context>/
│   ├── Entity/
│   ├── Enum/
│   ├── ValueObject/
│   └── Repository/
├── Application/<Context>/
│   ├── DTO/Request/
│   ├── DTO/Response/
│   ├── Mapper/
│   └── Service/
├── Infrastructure/Persistence/Doctrine/<Context>/
└── Interface/Http/<Context>/Controller/
```

## Contrato unico da API

Todo endpoint responde JSON no formato abaixo. Nao criar variacoes por controller.

### Sucesso unico

```json
{
  "data": {},
  "meta": { "request_id": "uuid" }
}
```

### Lista paginada

```json
{
  "data": [],
  "meta": {
    "request_id": "uuid",
    "pagination": {
      "page": 1,
      "per_page": 25,
      "total": 0,
      "total_pages": 0
    }
  }
}
```

### Erro

```json
{
  "error": {
    "code": "VALIDATION_FAILED",
    "message": "Um ou mais campos sao invalidos.",
    "details": [
      { "field": "email", "code": "INVALID_FORMAT", "message": "Informe um e-mail valido." }
    ]
  },
  "meta": { "request_id": "uuid" }
}
```

- `data` aparece somente em sucesso; `error`, somente em falha.
- `code` e estavel e voltado ao cliente; `message` e segura para exibir; `details` e uma lista e nunca exibe excecoes internas.
- Listas usam `page=1` e `per_page=25` por padrao, com maximo de 100.
- O `X-Request-Id` deve ser propagado ou gerado por middleware e refletido em `meta.request_id`.
- Codigos: `400 MALFORMED_REQUEST`, `401 UNAUTHENTICATED`, `403 FORBIDDEN`, `404 RESOURCE_NOT_FOUND`, `409 STATE_CONFLICT`, `422 VALIDATION_FAILED`, `429 RATE_LIMITED`, `500 INTERNAL_ERROR`.

## Processo para novo endpoint

1. Defina Request DTO, Response DTO, validacoes e contrato no `docs/API.md`.
2. Crie ou ajuste entidade, value object e interface de repository no dominio.
3. Implemente o service usando somente DTOs e interfaces de repository.
4. Implemente repository Doctrine e mapper.
5. Crie controller fino, rota e politica de autorizacao.
6. Adicione testes para controller, service e repository conforme o risco.

Historico de tentativas e respostas e imutavel. Toda consulta ou alteracao de dados pessoais deve restringir o resultado ao usuario autenticado.
