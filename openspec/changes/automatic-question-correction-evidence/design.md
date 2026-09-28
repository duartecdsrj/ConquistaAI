## Context

O worker inicia a evidência com as páginas de origem e a janela de duas páginas vizinhas. Ele só pesquisa o PDF quando reconhece um comando de busca na instrução. O snapshot imutável já contém o enunciado e as alternativas, mas esses textos não participam da localização automática.

## Goals / Non-Goals

**Goals:**

- Localizar evidências adicionais do PDF sem exigir comando na instrução.
- Usar referências determinísticas e minimizadas do snapshot, mantendo o PDF completo fora do executor.
- Preservar a busca explícita e a verificação de gabarito existentes.

**Non-Goals:**

- Alterar a API, a estrutura da proposta, a aprovação editorial ou o gabarito automaticamente.
- Usar IA, similaridade probabilística ou OCR novo para escolher páginas.
- Enviar o texto dos trechos automáticos aos logs.

## Decisions

- O worker selecionará o primeiro parágrafo não vazio do enunciado, após aparar espaços, e a alternativa não vazia mais próxima do centro da lista preservada no snapshot. São referências que representam as duas partes da questão sem depender da redação da instrução. Alternativa: pesquisar todas as alternativas; rejeitada para limitar custo e número de páginas anexadas.
- Cada referência será pesquisada pelo normalizador determinístico já usado na busca solicitada. Todas as páginas correspondentes entrarão na função existente de janela de duas páginas, combinadas e ordenadas com as páginas de origem. Alternativa: limitar ao primeiro acerto; rejeitada porque o mesmo trecho pode repetir-se em cabeçalho, comentário ou página de continuação.
- Trechos ausentes, curtos demais ou sem correspondência não interromperão a correção; o worker conservará as evidências originais e complementares já disponíveis. Alternativa: falhar a solicitação; rejeitada porque a fonte pode ter divergência de extração sem impedir uma proposta segura.
- O log usará um evento próprio por referência, com identificador lógico, hash SHA-256, páginas encontradas e páginas finais. Isso mantém diagnóstico sem registrar conteúdo da questão.

## Risks / Trade-offs

- [Trecho repetido amplia as imagens anexadas] → a janela continua deduplicada, ordenada e limitada à vizinhança de cada página encontrada.
- [PDF e snapshot divergem por falha de extração] → ausência de correspondência não bloqueia o worker.
- [Alternativa central não representa a página principal] → o primeiro parágrafo e as páginas de origem continuam como referências independentes.

## Migration Plan

Implantar somente o worker e os testes, sem migração de dados. Reverter o código restaura a composição anterior de evidências; solicitações já propostas preservam as páginas usadas em seu snapshot de proposta.

## Open Questions

Nenhuma.
