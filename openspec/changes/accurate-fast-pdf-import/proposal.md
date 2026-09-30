## Why

Os PDFs reais de TI fornecem 66 questões de múltipla escolha com gabarito explícito, mas o pipeline atual segmenta candidatos incorretos, repete falhas determinísticas e sofre disputa de locks. Isso reduz drasticamente a taxa de importação e torna documentos pequenos excessivamente lentos.

## What Changes

- Detectar e segmentar questões por alternativas e marcador de gabarito explícito, excluindo itens Certo/Errado do fluxo de múltipla escolha.
- Diferenciar falha determinística de conteúdo de indisponibilidade transitória do provider, sem retentativa e backoff para conteúdo inválido.
- Tornar a reivindicação concorrente de checkpoints resistente a deadlock e limitar lotes ao conjunto efetivamente elegível.
- Registrar no histórico totais verificáveis de candidatos detectados, processados, importados, duplicados e rejeitados com o motivo seguro.
- Adicionar uma regressão baseada nos cinco PDFs de TI com expectativa de 66 questões de múltipla escolha com gabarito.

## Capabilities

### New Capabilities

- `accurate-pdf-question-import`: segmentação e processamento resiliente de questões de múltipla escolha com gabarito explícito em PDFs.

### Modified Capabilities

- Nenhuma.

## Impact

Afeta o segmentador e o worker de importação PDF, repositório Doctrine de checkpoints, telemetria/histórico administrativo, configuração do worker e testes de regressão. Não altera endpoints públicos.
