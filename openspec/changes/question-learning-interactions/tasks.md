## 1. Contratos e fundação

- [ ] 1.1 Documentar em `docs/API.md` os contratos de estado, interações, anotações, comentários, relatos, filtros e explicações.
- [ ] 1.2 Documentar em `docs/FRONTEND.md` a barra de ações, drawers e estados responsivos.
- [ ] 1.3 Criar migration aditiva para estado pessoal, anotações, comentários, relatos, eventos de aprendizagem e execuções de explicação, com índices compostos.
- [ ] 1.4 Modelar entidades, enums, value objects e interfaces de repositório dos contextos de interação e sinais.

## 2. Interações pessoais e sociais

- [ ] 2.1 Implementar estado independente favorito, revisar depois e não dominei, com leitura e filtros combináveis por usuário.
- [ ] 2.2 Implementar anotação privada com criação, edição, datas e isolamento por usuário.
- [ ] 2.3 Implementar comentários públicos iniciais com referência opcional ao comentário pai.
- [ ] 2.4 Implementar relato categorizado de problema com descrição, estado e auditoria.
- [ ] 2.5 Expor DTOs, mappers, services, controllers e rotas autenticadas no envelope padrão.

## 3. Sinais adaptativos

- [ ] 3.1 Registrar evento imutável ao concluir tentativa com resultado, tempo, origem e referências de assunto.
- [ ] 3.2 Registrar evento distinto ao ativar ou desativar não dominei, inclusive após acerto.
- [ ] 3.3 Criar leitores de sinais/agregados pessoais para Performance, Review e futuras análises.
- [ ] 3.4 Integrar elegibilidade de análise assíncrona à finalização de caderno sem trabalho caro na resposta HTTP.

## 4. Explicação de questão por IA

- [ ] 4.1 Criar execução persistida, schema de resposta, contexto mínimo permitido e política anti-revelação antes da resposta.
- [ ] 4.2 Definir `QuestionExplanationProviderInterface`, factory configurável e adaptadores preparados para APIs externas.
- [ ] 4.3 Implementar worker Codex isolado no mesmo modelo de `process-question-corrections.php`, com claim, timeout, retries e logs sanitizados.
- [ ] 4.4 Implementar consumidor de explicação, idempotência e persistência de telemetria/resposta segura.
- [ ] 4.5 Expor solicitação e leitura de explicação autenticadas, restritas ao proprietário.

## 5. Experiência Quasar integrada

- [ ] 5.1 Criar módulo frontend DDD de interações de questão, repositório Axios, casos de uso e composição no container.
- [ ] 5.2 Criar barra de ações reutilizável para execução de caderno e visão completa, com atualização otimista e rollback.
- [ ] 5.3 Implementar painel de anotação, comentários, relato e explicação com loading, erro, vazio e responsividade.
- [ ] 5.4 Adicionar filtros de interações à listagem de questões com paginação real.

## 6. Qualidade e operação

- [ ] 6.1 Cobrir marcações independentes, isolamento privado, anotação, comentários, relatos e filtros em serviços e repositórios.
- [ ] 6.2 Cobrir eventos de resposta/não domínio, idempotência, schemas inválidos e isolamento de explicações.
- [ ] 6.3 Cobrir worker Codex com provider fake, falhas, retries e seleção de adaptador alternativo.
- [ ] 6.4 Executar migrations controladamente, testes API, build SPA e verificações estáticas; corrigir regressões atribuíveis.
- [ ] 6.5 Atualizar `docs/DEVELOPMENT_STATE.md` e gerar relatório técnico final em Markdown.
