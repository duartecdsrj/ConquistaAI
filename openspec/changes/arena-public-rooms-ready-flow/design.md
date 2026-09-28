## Context

Arena persiste salas sem visibilidade e só permite entrada por código. O backend já exige assuntos confirmados para iniciar, mas a SPA não expõe seleção, confirmação ou restauração da sala pela URL. A taxonomia disponível hoje é administrativa, portanto não pode ser usada pela Arena de usuários comuns.

## Goals / Non-Goals

**Goals:**

- Permitir descoberta e entrada segura de salas públicas.
- Permitir ao criador administrar suas salas privadas em espera.
- Completar o fluxo de espera até o início do duelo.

**Non-Goals:**

- Tornar detalhes, participantes ou questões de salas privadas públicos.
- Alterar regras de pontuação, seleção congelada ou acesso a salas em andamento.

## Decisions

- Persistir `visibility` com `PRIVATE` como padrão reversível para registros existentes. Salas públicas continuam com código interno, mas o ingresso público é pelo UUID e não o devolve na listagem. Alternativa: código nulo; rejeitada para preservar compatibilidade operacional.
- Expor duas listagens paginadas: públicas em `WAITING`, visíveis a autenticados; privadas criadas pelo usuário atual, independentemente de participante. Ambas retornam apenas resumo seguro. Alternativa: uma lista única com filtro; rejeitada para evitar que filtro de cliente revele salas privadas.
- Adicionar consulta autenticada de assuntos disponíveis à Arena e usar seleção múltipla Quasar limitada a `subjectsPerPlayer`, seguida de confirmação explícita. A URL `duel` é restaurada ao abrir a página. Alternativa: iniciar automaticamente após entrar; rejeitada porque a regra exige escolha individual de assuntos.

## Risks / Trade-offs

- [Salas públicas ociosas acumulam] → a listagem inclui somente `WAITING` e respeita paginação.
- [Entradas concorrentes excedem capacidade] → o repositório/serviço valida estado e lotação na transação.
- [URL aponta para sala privada de terceiro] → a API mantém `404` sem revelar sua existência.

## Migration Plan

Adicionar coluna com padrão `PRIVATE`, implantar API e SPA no mesmo ciclo. Reversão mantém a coluna sem uso; salas existentes seguem privadas.

## Open Questions

Nenhuma.
