## Context

O worker pesquisa automaticamente o primeiro parágrafo e uma alternativa central do snapshot. Alternativas curtas ou recorrentes retornam muitas páginas, e cada janela de duas páginas amplia o conjunto visual antes do prazo global do Codex.

## Goals / Non-Goals

**Goals:**

- Localizar evidências pelo trecho mais distintivo do enunciado.
- Reduzir páginas anexadas sem remover evidências explícitas ou de gabarito.
- Preservar logs sem texto da questão.

**Non-Goals:**

- Alterar rotas, payloads, dados persistidos ou usar IA/OCR na seleção.
- Mudar a janela de páginas, a busca explícita ou a regra de gabarito.

## Decisions

- Dividir o enunciado por quebras de linha, aparar espaços e remover somente caracteres não alfanuméricos nas extremidades de cada linha. A linha útil de maior comprimento será a única referência automática. Isso privilegia um trecho mais distintivo sem reconstruir conteúdo. Alternativa: usar o enunciado inteiro; rejeitada porque pode divergir no PDF por layout.
- Não pesquisar alternativas automaticamente. Alternativa: usar a alternativa central atual; rejeitada porque opções frequentes ampliam resultados sem melhorar a localização.
- Em empate, preservar a primeira linha na ordem do snapshot. A decisão é determinística e não expõe conteúdo adicional.

## Risks / Trade-offs

- [A maior linha não aparece integralmente no PDF] → buscas explícitas e páginas de origem continuam disponíveis.
- [Uma linha longa é cabeçalho recorrente] → a busca usa a normalização existente e a seleção elimina alternativas genéricas, reduzindo a principal ampliação observada.

## Migration Plan

Implantar worker e testes sem migração. Reverter restaura a seleção anterior; solicitações já concluídas preservam suas evidências.

## Open Questions

Nenhuma.
