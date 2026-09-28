## Context

O backend PHP/Slim/Doctrine/MySQL é a fonte de verdade, a SPA é Vue/Quasar e Socket.IO já autentica JWT em `/ws/socket.io`.

## Goals / Non-Goals

**Goals:** salas privadas persistidas, seleção congelada de questões PUBLISHED, tempo/correção/pontuação no servidor e reconexão segura.

**Non-Goals:** matchmaking, ranking, torneios, espectadores, Redis e mudanças nas métricas existentes.

## Decisions

- Tabelas Arena preservam fase, participantes, escolhas, questões, respostas e placares; transações e índices únicos garantem idempotência.
- O PHP grava abertura, prazo e recebimento; ordem é `received_at, id`. REST executa comandos e a API publica evento de sala após commit.
- Socket.IO é entrega, não fonte de verdade: o cliente recebe evento, busca estado sanitizado e reentra na sala após reconectar.
- DTO aberto oculta gabarito, explicação, escolhas adversárias e questões futuras; DTO encerrado revela somente a questão atual.
- Seleção equilibrada percorre assuntos escolhidos e pretere questões recentes quando possível; resposta final gera tentativa com contexto `ARENA_DUELO`.

## Risks / Trade-offs

- Gateway único → publicação isolada para futura troca por Redis/pub-sub; timeout é encerrado idempotentemente em leituras/comandos até existir worker.
