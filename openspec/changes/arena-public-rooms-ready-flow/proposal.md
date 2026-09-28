## Why

Duelo é acessível apenas por código e a tela não permite escolher assuntos nem confirmar prontidão; por isso participantes permanecem em espera e o criador não consegue iniciar. O produto precisa expor salas públicas e dar ao criador visibilidade das salas privadas que administra.

## What Changes

- Permite criar duelos `PUBLIC` ou `PRIVATE`; salas públicas podem ser listadas e acessadas sem código, enquanto privadas mantêm o ingresso por código.
- Lista salas públicas em espera para qualquer usuário autenticado e salas privadas criadas pelo usuário atual somente para seu criador.
- Completa a tela de espera com seleção real de assuntos, confirmação de prontidão e restauração da sala pela URL.
- Mantém a autorização por participante para detalhes e ações do duelo.

## Capabilities

### New Capabilities

- `arena-room-discovery`: descoberta paginada de salas públicas e privadas do criador.

### Modified Capabilities

- `arena-duel`: inclui visibilidade da sala e o fluxo obrigatório de seleção de assuntos/prontidão antes do início.

## Impact

Afeta migration e entidades Arena, contratos e rotas autenticadas, serviços/repositórios Doctrine, módulos DDD e tela Quasar Arena, documentação API/FRONTEND e testes. Não altera a pontuação nem expõe detalhes de participantes de salas privadas a terceiros.
