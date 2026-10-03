## Why

As estatísticas atuais mostram resultados agregados, mas não deixam claro como as práticas se distribuem pela árvore de assuntos nem como o estudo está planejado até a prova. Um mapa de estudo hierárquico com cronograma Gantt permitirá acompanhar assuntos, subassuntos, dependências, conclusão e desempenho real no mesmo contexto.

## What Changes

- Adicionar uma visão autenticada de mapa de estudo por concurso, organizada pela taxonomia canônica em assuntos e subassuntos.
- Exibir, para cada nó, tentativas concluídas, acertos, taxa de acerto e evidência calculadas a partir das práticas do usuário.
- Adicionar um cronograma persistido no estilo Gantt, com datas planejadas, posição temporal e dependências explícitas entre assuntos.
- Permitir marcar um assunto planejado como estudado ou concluído sem removê-lo da árvore nem da linha do tempo.
- Integrar a visão ao módulo Performance existente, sem alterar o histórico imutável de tentativas ou respostas.

## Capabilities

### New Capabilities

- `study-map-performance`: fornece um mapa hierárquico de estudo por concurso, cronograma Gantt persistido, dependências entre assuntos e estatísticas reais por assunto e subassunto.

### Modified Capabilities

- Nenhuma.

## Impact

- Backend Performance: consulta agregada, entidades de cronograma, migração, DTOs, serviços, repositórios Doctrine, rotas autenticadas e testes.
- Frontend Performance: contrato tipado, caso de uso, repositório Axios, composable e tela Quasar responsiva.
- Documentação: contrato em `docs/API.md`, experiência em `docs/FRONTEND.md` e estado de desenvolvimento.
