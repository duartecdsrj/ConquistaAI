# Roadmap

## Fase 0 - Fundacao arquitetural

Entregue nesta etapa: decisoes de arquitetura, modelo de dados, API e plano. A infraestrutura do MVP agora inclui Compose, frontend Vue/Quasar, healthchecks, CORS configuravel, migrations e scripts de backup/restauracao. A proxima acao e inicializar o monorepo, o Docker Compose, as dependencias e a primeira migration.

## Fase 1 - MVP

Objetivo: um usuario autenticado consegue encontrar questoes publicadas, criar um caderno com selecao congelada, responder em modo estudo/prova e consultar estatisticas basicas.

1. Infraestrutura: Compose, Nginx, PHP 8.3, Vue/Quasar, MySQL, `.env.example`, healthchecks, logs estruturados, scripts de backup/restauracao.
2. Identity: migration de usuarios/papeis/sessoes, login, refresh rotativo, logout, RBAC, rate limit e testes de isolamento.
3. Catalogo: CRUD administrativo de concursos, cargos, editais, arvore de assuntos e tags.
4. Questoes: CRUD com alternativas, assuntos/tags, publicacao, sanitizacao e filtros.
5. Importacao: leitura JSON/CSV, preview, validacao por linha, deteccao de candidatos a duplicidade e relatorio/confirmacao.
6. Cadernos: criacao por filtros, persistencia da lista selecionada, inicio, navegacao e modos estudo/prova.
7. Historico e metricas basicas: attempts/answers imutaveis, total, acertos, erros, percentual, tempo medio e desempenho por assunto.

Critério de aceite: nenhum usuario consegue ler ou alterar registros pessoais de outro; uma selecao de caderno nao muda; resposta anterior permanece auditavel; importacao invalida nao cria questao; testes cobrem autenticacao, RBAC, caderno, tentativa e estatisticas.

## Fase 2 - Aprendizado orientado por dados

- Simulados com distribuicao, cronometro e gabarito bloqueado ate a entrega.
- Dashboard pessoal limpo, evolucao temporal e detalhes por assunto/dificuldade.
- Fila de revisao com regra inicial: erro ou marcacao manual agenda para D+1; acerto em revisao evolui para D+3, D+7 e D+14; novo erro retorna a D+1.
- `PerformanceAnalysisService`, confianca, pontos fortes/fracos e `StudyRecommendationService`.

## Fase 3 - IA assistiva

- `AIProvider`, configuracao por ambiente e telemetria segura.
- `StudyContextBuilder` com dados minimizados e isolamento estrito.
- Conversas gerais e contextuais, com politica anti-spoiler para simulados.
- Geracao de questoes em revisao editorial, nunca publicacao automatica.
- Esqueleto de dominio para Tutor, sem automacao pedagogica completa.

## Fase 4 - Evolucao

- Repeticao espacada calibrada por desempenho e dificuldade.
- Metas, notificacoes e PWA.
- Gamificacao apenas se apoiar a rotina de estudo, nao como prioridade.
- Metricas exportaveis, retencao de dados configuravel e reforcos operacionais.

## Riscos e decisoes pendentes

O MVP nao deve importar Excel nem integrar fornecedor de IA. Ambos sao extensoes posteriores. A taxonomia de assuntos deve ser cadastrada por edital na administracao; a carga inicial da Transpetro sera feita por importacao/seed versionado depois que o formato editorial for validado. Definiremos antes da implementacao visual o formato de conteudo rico para enunciados (Markdown sanitizado e imagens em armazenamento local na primeira versao).
