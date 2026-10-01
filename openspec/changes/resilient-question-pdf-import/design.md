## Context

Cabeçalhos podem permanecer no enunciado, imagens são extraídas antecipadamente e falhas de retry podem deixar jobs sem checkpoint recuperável.

## Goals / Non-Goals

**Goals:** metadados explícitos, enunciado limpo, marcadores de figura, retomada idempotente, menor trabalho repetido e histórico temporal.

**Non-Goals:** inferir metadados ou gabarito sem evidência, publicar automaticamente ou apagar dados anteriores.

## Decisions

- Figuras exigem marcador `[[FIGURA:n]]` e âncora `ANCHORED`.
- Cabeçalho só é removido quando contém banca, concurso, cargo e ano explícitos.
- Lease é renovado por checkpoint e candidato persistido é pulado.
- O contexto taxonômico é limitado ao relevante, com fallback.

## Risks / Trade-offs

- Formato incomum → manter enunciado e não inventar metadado.
- Figura ambígua → sinalizar e não anexar.
