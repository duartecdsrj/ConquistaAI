## Why

A importação PDF executa a análise dos candidatos de forma serial, trata falhas de conteúdo como indisponibilidade global e descarta duplicatas sem aproveitar um gabarito oficial recuperado posteriormente. Isso torna lotes grandes lentos, pode encerrá-los indevidamente e exibe datas de início/fim incorretas no histórico.

## What Changes

- Separar falhas transitórias do executor de falhas de validação de um candidato, com retentativa limitada por candidato e continuidade segura do lote.
- Preservar o início efetivo do job e registrar finalização somente em estado terminal.
- Processar candidatos de um mesmo PDF com concorrência configurável, checkpoints atômicos e recuperação idempotente.
- Ao encontrar duplicata, promover somente o gabarito explicitamente oficial da nova evidência sobre ausência ou estimativa existente, preservando auditoria e sem substituir fonte oficial por inferência.
- Expor no histórico a execução e as contagens coerentes após retomadas concorrentes.
- Remover, após a revisão e validação, questões e histórico de importação existentes.

## Capabilities

### New Capabilities

- `resilient-parallel-pdf-import`: execução concorrente, recuperação, tempos e classificação de falhas para jobs de PDF.
- `official-answer-key-reconciliation`: reconciliação segura de gabarito oficial quando uma questão importada é duplicada.

### Modified Capabilities

- Nenhuma.

## Impact

Afeta o worker PHP e sua persistência Doctrine, migrations, executor Codex, configuração Compose, histórico administrativo de importações, testes e documentação de API/frontend.
