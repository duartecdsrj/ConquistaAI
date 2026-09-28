## Why

A ConquistaAI ainda não oferece uma experiência social de prática síncrona. O modo Duelo da Arena cria uma primeira disputa privada, justa e recuperável, reutilizando o banco de questões e mantendo a correção e a pontuação no servidor.

## What Changes

- Adiciona Arena > Duelo para criar, entrar, preparar, jogar e concluir partidas privadas com dois ou mais usuários autenticados.
- Adiciona persistência de partida, participantes, assuntos, questões congeladas, respostas, tempos, placares e resultado, incluindo contexto de desempenho `ARENA_DUELO`.
- Adiciona rotas autenticadas para ciclo de vida do duelo e sincronização de estado.
- Estende o canal Socket.IO existente com salas de duelo, eventos de atualização e recuperação de estado após reconexão.
- Adiciona área Arena no shell e telas Quasar responsivas com estados reais de carregamento, erro, vazio e conexão.

## Capabilities

### New Capabilities

- `arena-duel`: Duelo privado multiplayer com preparação, seleção equilibrada e congelada de questões, resolução síncrona, resultado e histórico persistido.
- `arena-realtime`: Entrega autenticada de atualizações de duelo e recuperação consistente do estado após reconexão.
- `arena-performance-provenance`: Registro de respostas de Arena com origem de desempenho explícita.

### Modified Capabilities

- Nenhuma.

## Impact

- Backend PHP: novo contexto Arena em Domain, Application, Infrastructure/Doctrine e Interface/Http; migration MySQL; contratos em `docs/API.md`.
- Frontend Vue/Quasar: novo módulo DDD Arena, tela e componentes em `apps/web/src`, integração ao shell e ao Socket.IO já implantado.
- Tempo real: `apps/web/realtime-server.mjs` e publicação autenticada de eventos pela API. Redis não é introduzido nesta primeira versão monolítica; a persistência transacional é a fonte de verdade.
