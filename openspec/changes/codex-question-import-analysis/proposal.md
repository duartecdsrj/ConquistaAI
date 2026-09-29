## Why

A importação de PDF atual delega a extração a um adaptador genérico e não mantém evidências suficientes para identificar inconsistências de enunciado, alternativas, gabarito e imagens. A revisão precisa de uma análise estruturada por questão, provedores intercambiáveis e avisos seguros para que o aluno saiba que há dúvida editorial sem receber resposta antecipada.

## What Changes

- Substituir o processamento de análise de questões de PDFs por um provider estruturado, com Codex como implementação inicial em worker isolado e adaptadores selecionáveis por configuração.
- Exigir análise individual, com evidências de páginas, de enunciado, alternativas, metadados explicitamente documentados, relações de colunas/afirmações e figuras vinculadas à posição correta.
- Persistir relatório imutável por questão e por job, incluindo veredito de coerência, sinais de possível divergência de gabarito, discrepância visual, confiança, proveniência, provider/modelo, duração e uso de tokens.
- Exibir um aviso permanente e não expansível na resolução e revisão quando a análise marcar possível incoerência; o aviso não revela gabarito ou alternativa.
- Incluir o sinal editorial no contexto seguro de explicação após tentativa concluída, para explicar a interpretação provável da banca e a ressalva detectada sem transformar suspeita em fato.

## Capabilities

### New Capabilities

- `codex-question-pdf-import`: análise individual e auditável de questões de PDF por provider estruturado, com Codex inicial, imagens, metadados, validações e telemetria.
- `question-quality-signals`: persistência e apresentação segura de alertas editoriais de coerência em revisão e resolução.

### Modified Capabilities

- `question-ai-explanations`: a explicação pós-resposta deve usar o alerta editorial e a interpretação da banca sem revelar resposta antes da tentativa concluída.

## Impact

- Question Bank: job de PDF, worker, contratos de provider, escritor Doctrine, ativos e migration de telemetria/achados.
- API e SPA administrativa: histórico e revisão clara de achados de importação; API e SPA de resolução: aviso seguro por questão.
- Infraestrutura: imagem/worker Codex reutilizável e variáveis de seleção de provider, modelo, timeout e orçamento.
- Documentação: contratos de importação, explicação e experiência de revisão/resolução.
