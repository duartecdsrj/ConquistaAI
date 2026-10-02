## Why

A sessão atual congela vários cards antes da primeira resposta, o que torna a revisão desatualizada e impede a continuidade natural da fila. A pessoa também não sabe quantos cards estão vencidos nem consegue continuar estudando antecipadamente quando não houver vencidos.

## What Changes

- A revisão diária passa a entregar um único card atual e a selecionar o próximo somente depois de registrar a classificação anterior.
- A API informa a quantidade atual de cards vencidos e se há cards elegíveis para revisão antecipada.
- Quando não existirem cards vencidos, a interface oferece iniciar uma revisão antecipada; essa fila prioriza cards com vencimento mais próximo sem alterar o critério da fila diária.
- A classificação devolve o próximo card e o estado atualizado da fila, sem exigir que o cliente reconstrua ou congele a seleção.

## Capabilities

### New Capabilities

- `continuous-review-queue`: Fila individual de revisão com próximo card dinâmico, contagem de vencidos e avanço opcional.

### Modified Capabilities

- Nenhuma.

## Impact

- API e documentação do contexto Review.
- Casos de uso, contratos de repositório e persistência Doctrine de Review.
- Módulo DDD e experiência Quasar de revisão no frontend.
- Testes unitários, de integração e de interface proporcionais ao novo fluxo.
