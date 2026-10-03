## Por que

O cronograma atual apresenta o mapa em Gantt, mas ainda não oferece a leitura completa de planejamento e desempenho mostrada na referência: resumo de carga, navegação temporal, controles de visualização e indicadores laterais. Isso torna mais difícil identificar o andamento do plano, a distribuição do esforço e os assuntos que exigem atenção.

## O que muda

- Reestruturar a página de Cronograma como um painel Gantt de duas colunas, com cartões de resumo, navegação pelo período, filtros e tabela hierárquica de assuntos.
- Exibir, na coluna direita, progresso geral, distribuição da carga planejada por disciplina, pontos fortes e fracos obtidos das práticas e legenda do cronograma.
- Registrar estimativa de carga por item do cronograma e data de prova por mapa do usuário, para que tempo estimado, concluído, restante e dias até a prova sejam dados persistidos.
- Estender o contrato do mapa para fornecer os agregados de planejamento e desempenho necessários à interface, sem inferir conclusão a partir de respostas de flashcards.
- Manter a experiência utilizável em celular, empilhando os indicadores e preservando a leitura da programação.

## Impacto

- Especificação `study-map-performance`.
- API de desempenho, DTOs, serviços, repositórios Doctrine e migrações de cronograma.
- Página e componentes de Cronograma no frontend, incluindo o fluxo DDD de consumo da API.
