## ADDED Requirements

### Requirement: Logs seguros do worker
O worker SHALL registrar transições de execução com ID da solicitação, fase, duração e resultado, sem segredos, prompt integral ou conteúdo da questão.

#### Scenario: Proposta concluída
- **WHEN** o Codex devolver proposta válida
- **THEN** o worker registra `proposed` com duração e atualiza a solicitação

### Requirement: Timeout e limpeza
O worker SHALL encerrar uma execução que exceder o limite configurado, limpar os temporários e marcar a solicitação como `FAILED`.

#### Scenario: Codex sem resposta
- **WHEN** o subprocesso exceder o timeout
- **THEN** a solicitação não permanece em `PROCESSING` e a próxima fila pode ser processada

### Requirement: Execução não interativa e restrita
O worker SHALL executar o Codex sem solicitar confirmação humana, mantendo sandbox somente leitura e acesso apenas ao diretório temporário da questão.

#### Scenario: Correção em segundo plano
- **WHEN** a fila entregar uma solicitação válida
- **THEN** o worker inicia a análise sem prompt de permissão e sem ampliar o escopo de arquivos acessíveis
