# Convencoes de Implementacao e API

Este documento e normativo para o backend. Ele complementa a arquitetura e deve ser seguido por pessoas e IAs.

## Separacao de camadas

```text
HTTP Request -> Request DTO -> Controller -> Service -> Repository Interface -> Doctrine Repository
                                                                        |
HTTP Response <- Response DTO <- Mapper <------------------------ Domain Entity
```

| Camada | Pode fazer | Nao pode fazer |
| --- | --- | --- |
| Controller | converter entrada validada em chamada de service, autorizar e responder | regra de negocio, SQL, Doctrine, repository, entidade |
| Request DTO | representar e validar entrada HTTP | acessar banco, chamar service |
| Service | executar caso de uso, transacionar, chamar repository por interface | conhecer HTTP, Slim, PDO ou EntityManager |
| Repository | persistir e consultar com Doctrine ORM | receber HTTP DTO, montar resposta HTTP |
| Mapper | converter entidade/value object/resultado em DTO | consultar banco, alterar estado, chamar service |
| Response DTO | representar saida publica tipada | expor entidade ou detalhe interno |

Request e Response DTOs sao `final readonly`, com tipos explicitos. Nenhuma entidade Doctrine, array de banco ou excecao interna pode atravessar a fronteira HTTP.

## Persistencia

Interfaces de repository pertencem ao dominio e implementacoes pertencem a `Infrastructure/Persistence/Doctrine`. Toda implementacao usa `EntityManagerInterface`, entidades mapeadas por atributos e `QueryBuilder`/DQL com parametros. PDO, `mysqli` e SQL cru sao proibidos em codigo de aplicacao.

Services dependem de interfaces de repository por injecao de dependencia. Somente services chamam repositories. Operacoes com mais de um agregado sao transacionais no service.

## Contrato de resposta

```json
{ "data": {}, "meta": { "request_id": "uuid" } }
```

```json
{
  "data": [],
  "meta": {
    "request_id": "uuid",
    "pagination": { "page": 1, "per_page": 25, "total": 0, "total_pages": 0 }
  }
}
```

```json
{
  "error": {
    "code": "VALIDATION_FAILED",
    "message": "Um ou mais campos sao invalidos.",
    "details": [{ "field": "email", "code": "INVALID_FORMAT", "message": "Informe um e-mail valido." }]
  },
  "meta": { "request_id": "uuid" }
}
```

`ApiResponseFactory` e `ApiProblemFactory` sao as unicas classes autorizadas a montar esses envelopes. `X-Request-Id` e propagado ou criado por middleware e sempre volta em `meta.request_id`.

Paginacao usa `page=1` e `per_page=25` por padrao, com maximo 100. Codigos estaveis: `MALFORMED_REQUEST` (400), `UNAUTHENTICATED` (401), `FORBIDDEN` (403), `RESOURCE_NOT_FOUND` (404), `STATE_CONFLICT` (409), `VALIDATION_FAILED` (422), `RATE_LIMITED` (429) e `INTERNAL_ERROR` (500).

## Checklist para endpoint

1. Criar Request DTO, Response DTO e validacoes.
2. Criar ou ajustar entidade, value object e contrato de repository.
3. Implementar service usando DTOs e contrato de repository.
4. Implementar mapper e repository Doctrine.
5. Criar controller fino, rota e politica de autorizacao.
6. Documentar contrato e cobrir o fluxo com testes.
