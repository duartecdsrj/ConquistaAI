# Convencoes da API

Esta API segue as regras da raiz. Em caso de conflito, este arquivo detalha a aplicacao delas no backend PHP.

- Use PHP 8.3+, `declare(strict_types=1)`, classes `final` e DTOs `readonly`.
- Entidades usam atributos Doctrine ORM e ficam em `Domain`.
- Interfaces de repository pertencem ao dominio; implementacoes Doctrine pertencem a infraestrutura.
- Controllers nao conhecem entidades, repositories ou `EntityManagerInterface`.
- Services nao conhecem Slim, PSR-7, PDO ou `EntityManagerInterface`.
- `ApiResponseFactory` e `ApiProblemFactory` sao os unicos responsaveis pelos envelopes HTTP.
- `PaginationRequestDto` e `PaginatedResponseDto` sao obrigatorios em endpoints de colecao.
- Excecoes de dominio sao convertidas por middleware global em `ApiProblemResponseDto`.
- Nenhuma entidade ou objeto interno deve chegar ao serializador HTTP sem Response DTO e mapper.
