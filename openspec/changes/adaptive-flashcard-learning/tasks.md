## 1. Contratos e fundação Review

- [ ] 1.1 Documentar os contratos HTTP de revisão, sessão, classificação, mapa de domínio e análise de caderno em `docs/API.md`.
- [ ] 1.2 Documentar o módulo SPA Review e seus estados reais em `docs/FRONTEND.md`.
- [ ] 1.3 Criar migration aditiva com tabelas, chaves, unicidade e índices para cards, progresso, histórico, domínio, execução e auditoria.
- [ ] 1.4 Modelar entidades, enums, value objects e interfaces de repositório do contexto Review, referenciando assuntos canônicos.
- [ ] 1.5 Implementar adaptadores Doctrine, transações e mapeamentos de persistência do contexto Review.

## 2. Algoritmos e revisão individual

- [ ] 2.1 Implementar estratégias substituíveis de repetição espaçada, estimativa de domínio e cálculo central de prioridade com configuração única.
- [ ] 2.2 Implementar criação/reutilização deduplicada de flashcards por fingerprint normalizada e associação segura a conceitos canônicos.
- [ ] 2.3 Implementar montagem de sessão diária e revisão rápida priorizadas para o usuário autenticado.
- [ ] 2.4 Implementar registro de classificação de revisão, atualização do progresso e histórico imutável.
- [ ] 2.5 Implementar leitura do mapa hierárquico de domínio e do resultado persistido de análise de caderno.

## 3. Análise de caderno por Codex

- [ ] 3.1 Mapear e reutilizar a infraestrutura de job, telemetria e provider estruturado do corretor de questões para análise de caderno.
- [ ] 3.2 Implementar resumo mínimo de desempenho, schema/DTO validado de resposta Codex e enumeração de ações permitidas.
- [ ] 3.3 Implementar execução assíncrona idempotente por caderno/versão, retentativas limitadas e auditoria sem cadeia de raciocínio.
- [ ] 3.4 Implementar aplicador transacional de ações, atualização de sinais de domínio e antecipação de cards existentes.
- [ ] 3.5 Integrar o disparo não bloqueante à finalização de caderno e expor o estado/resultados da análise.

## 4. API e experiência Quasar

- [ ] 4.1 Criar DTOs, mappers, controladores finos, rotas e autorização das operações Review no envelope padrão.
- [ ] 4.2 Criar módulo frontend DDD Review, repositório Axios, casos de uso e composição no container.
- [ ] 4.3 Implementar página Quasar de revisão diária e rápida, card com revelação, classificação, retomada e responsividade.
- [ ] 4.4 Implementar resultado de análise de caderno e mapa navegável de domínio com carregamento, erro, vazio e processamento.
- [ ] 4.5 Integrar navegação e apresentação do resultado à experiência existente de cadernos e desempenho.

## 5. Qualidade, operação e encerramento

- [ ] 5.1 Cobrir estratégias, deduplicação, atualização por questões, idempotência, falha/JSON inválido do Codex e cenários de histórico baixo/alto com testes de unidade.
- [ ] 5.2 Cobrir serviços, repositórios e controladores Review em proporção ao risco, incluindo isolamento por usuário.
- [ ] 5.3 Executar migrations controladamente, testes API, build/testes SPA e verificações estáticas; corrigir regressões atribuíveis.
- [ ] 5.4 Atualizar `docs/DEVELOPMENT_STATE.md` com entrega, decisões, pendências e validações e preparar o relatório final.
