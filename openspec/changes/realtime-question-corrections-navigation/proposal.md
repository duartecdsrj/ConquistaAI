## Por quê

A correção conclui em worker assíncrono e hoje exige que o usuário permaneça na mesma tela e consulte manualmente o resultado. Atualizações também descartam a seção atual, interrompendo o fluxo de estudo.

## O que muda

- Notificação WebSocket autenticada quando uma correção muda de estado, especialmente em `PROPOSED` e `FAILED`.
- Notificação global com atalho para revisar/aprovar a proposta, independentemente da tela atual.
- O usuário pode ignorar permanentemente, neste navegador, um resultado específico; a dispensa não altera a solicitação nem o histórico editorial.
- Persistência da seção atual em URL, inclusive ao recarregar.
- Correção disponível no Banco de Questões e no Caderno.
- Caderno permite próxima questão sem responder, próximo assunto e assunto anterior.
- Worker registra eventos seguros, executa sem interação e encerra correções excedentes com falha recuperável.
- Para identificação de figura, o worker renderiza a página de origem e duas páginas anteriores e posteriores, sem expor o PDF completo ao executor.
- Diálogo de revisão mostra prévia completa de enunciado e alternativas e permite reenvio com sugestões, sem sobrescrever solicitações anteriores.
- Solicitações com falha também podem ser reenviadas pelo administrador; a aprovação permanece limitada ao estado PROPOSED.
- Após aprovação, o Caderno aberto recarrega o conteúdo editorial da questão sem alterar sua seleção, posição ou respostas.
- Propostas podem indicar uma lista ordenada `figures`. Cada item aponta uma página de evidência e aparece no enunciado pelo marcador `[[FIGURA:n]]`; ao aprovar, cada prévia extraída é promovida como ativo autenticado e exibida pelo Caderno no marcador correspondente.

## Capacidades

### Novas capacidades

- `notificacoes-correcao-em-tempo-real`: entrega e apresentação global do resultado do worker.
- `navegacao-de-caderno-por-assunto`: avanço livre entre questões e grupos de assunto.
- `execucao-resiliente-do-worker-de-correcao`: logs, timeout, limpeza e execução não interativa.

### Capacidades modificadas

- `correcao-assistida-de-questao`: solicitação e aprovação passam a notificar globalmente.
- `navegacao-da-aplicacao`: seção ativa passa a ser restaurável pela URL.

## Impacto

API e worker PHP, Compose/Nginx, SPA Vue/Quasar, rotas da aplicação, contrato de correção e testes de integração/visual.
