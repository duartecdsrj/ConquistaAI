## 1. Checkpoints e resiliência

- [x] 1.1 Criar migration, entidade e repositório Doctrine para checkpoints de candidatos, com lock, estado, tentativas e próxima execução.
- [x] 1.2 Refatorar o serviço para materializar, reivindicar e consolidar checkpoints sem sobrescrever os timestamps do job.
- [x] 1.3 Classificar falhas transitórias e de validação, com retry por candidato, backoff e mensagens administrativas seguras.

## 2. Paralelismo e gabarito

- [x] 2.1 Configurar consumidores paralelos e limite operacional para o worker PDF.
- [x] 2.2 Reconciliar duplicata com gabarito oficial sem substituir gabarito oficial existente nem conteúdo editorial.

## 3. Contrato, validação e limpeza

- [x] 3.1 Atualizar histórico administrativo e documentação para tempos, estados e contagens consolidados.
- [x] 3.2 Adicionar testes de timestamp, retry isolado, concorrência/idempotência e promoção de gabarito oficial.
- [x] 3.3 Validar containers, testes focais e limpeza autorizada de questões e histórico de importação.
