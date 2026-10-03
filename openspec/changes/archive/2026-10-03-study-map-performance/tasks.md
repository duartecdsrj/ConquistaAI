## 1. Modelo e contrato do cronograma

- [x] 1.1 Documentar as rotas de leitura e escrita do mapa e cronograma em `docs/API.md`.
- [x] 1.2 Criar migration, entidades e interfaces de repositório para itens de cronograma e dependências por usuário e concurso.
- [x] 1.3 Criar DTOs tipados para mapa, nós hierárquicos, agendamento, conclusão e dependências.
- [x] 1.4 Implementar validações de escopo, datas, estado e ciclos de dependência.

## 2. Agregação de desempenho e backend HTTP

- [x] 2.1 Estender a porta de estatísticas com respostas finais e nós canônicos aplicáveis ao concurso.
- [x] 2.2 Implementar a consulta Doctrine restrita ao usuário, concurso e período, sem SQL cru.
- [x] 2.3 Implementar o serviço que monta a árvore, agrega métricas sem duplicação e combina o cronograma confirmado.
- [x] 2.4 Criar controllers finos e registrar as rotas autenticadas de leitura, agendamento, conclusão e dependências.
- [x] 2.5 Adicionar testes unitários para agregação, ausência de dados, dependências e conclusão preservando datas.
- [x] 2.6 Adicionar testes Doctrine e HTTP para isolamento por usuário, período inválido e ciclo de dependência.

## 3. Mapa Gantt na SPA

- [x] 3.1 Criar tipos de domínio, métodos de repositório e casos de uso Performance para mapa e cronograma.
- [x] 3.2 Implementar o adaptador Axios e o estado confirmado em `usePerformance`.
- [x] 3.3 Implementar a seção Quasar com seleção de concurso, período, resumo, árvore expansível e escala Gantt.
- [x] 3.4 Implementar os controles de agendar, declarar dependência e marcar estudado ou concluído sem deslocar a faixa temporal.
- [x] 3.5 Tratar carregamento, falha, recorte vazio e nós sem dados sem calcular estatísticas ou planejamento no cliente.
- [x] 3.6 Atualizar `docs/FRONTEND.md` com o fluxo e a responsividade do módulo.

## 4. Validação integrada

- [x] 4.1 Executar lint e testes focais do backend, incluindo isolamento por usuário e ciclo de dependência.
- [x] 4.2 Executar `npm run build` da SPA e revisar o Gantt em desktop e mobile com dados reais.
- [x] 4.3 Atualizar `docs/DEVELOPMENT_STATE.md`, validar a mudança OpenSpec e arquivar após a entrega completa.
