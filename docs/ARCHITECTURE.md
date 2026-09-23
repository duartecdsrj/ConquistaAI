# Arquitetura

## Decisoes

O sistema sera um monolito modular implantado como tres processos: SPA frontend, API REST e banco MySQL. Esta escolha preserva limites claros de dominio, transacoes consistentes e testes simples, sem custo de coordenar microservicos. Nginx sera a unica entrada publica. Redis entrara inicialmente para rate limiting e armazenamento de refresh-token/revogacao caso a implementacao exija compartilhamento entre instancias; nao sera requisito para regras de negocio.

O produto nao sera acoplado a Transpetro. Concurso, cargo/enfase e edital sao dados administrativos. A arvore de assuntos e reutilizavel por edital e por questao.

## Contextos de dominio

| Contexto | Responsabilidade |
| --- | --- |
| Identity & Access | usuarios, papeis, senha, tokens, auditoria de autenticacao |
| Catalog | concursos, cargos, editais, disciplinas, arvore de assuntos e tags |
| Question Bank | questoes, alternativas, classificacao, fontes, status editorial e importacao |
| Study Sessions | cadernos, simulados, selecao congelada, navegacao e finalizacao |
| Performance | tentativas imutaveis, respostas, estatisticas, analise e recomendacoes |
| Review | fila de revisao e agenda simples de repeticao espacada |
| AI Assistance | conversas, contexto minimizado, provedores de IA e geracao editorial |

Cada contexto tera um modulo PHP proprio. Um modulo so conhece contratos publicos de outro; controllers coordenam casos de uso e nunca contem regra de negocio relevante.

## Monorepo

```text
.
├── apps/
│   ├── api/
│   │   ├── public/index.php
│   │   ├── config/
│   │   ├── migrations/
│   │   ├── src/
│   │   │   ├── Domain/
│   │   │   │   ├── Identity/
│   │   │   │   ├── Catalog/
│   │   │   │   ├── QuestionBank/
│   │   │   │   ├── Study/
│   │   │   │   ├── Performance/
│   │   │   │   ├── Review/
│   │   │   │   └── Assistant/
│   │   │   ├── Application/
│   │   │   ├── Infrastructure/
│   │   │   └── Interface/Http/
│   │   └── tests/
│   └── web/
│       ├── src/pages/
│       ├── src/components/
│       ├── src/composables/
│       ├── src/services/
│       ├── src/stores/
│       └── src/router/
├── docker/
│   ├── api/
│   ├── frontend/
│   └── nginx/
├── docs/
├── scripts/
├── compose.yaml
└── .env.example
```

No backend, `Domain` contem entidades, value objects, contratos e servicos puros; `Application` contem casos de uso e DTOs; `Infrastructure` implementa Doctrine, JWT, Redis, importadores e IA; `Interface/Http` contem rotas, controllers, middleware e serializacao. No frontend, paginas compoem componentes Quasar; stores mantem apenas estado de interface e chamadas vao para services tipados.

## Fluxos essenciais

### Autenticacao

1. `POST /auth/login` valida identificador, senha e bloqueio temporario.
2. A senha e verificada com Argon2id. Uma falha e registrada sem senha ou token em log.
3. A API devolve access token JWT curto (15 minutos) e refresh token aleatorio opaco (30 dias, configuravel).
4. O refresh e armazenado somente como hash, associado a dispositivo/sessao e pode ser revogado. O cliente recebe-o em cookie `HttpOnly`, `Secure`, `SameSite=Lax`; o access token fica apenas em memoria.
5. `POST /auth/refresh` rotaciona o refresh token. Reuso de token ja rotacionado revoga a familia da sessao.
6. Middleware autentica JWT e middleware RBAC aplica `ADMIN` ou `USER` por rota. Todo recurso pessoal tambem filtra por `user_id`, mesmo para usuarios autenticados.

### Resolucao e historico

Ao iniciar um caderno, as questoes selecionadas ja existem em `notebook_questions`; nenhuma consulta posterior muda sua composicao. Cada envio final de resposta cria uma `attempt` e uma `answer` novas. Uma mudanca antes da finalizacao cria nova resposta da mesma tentativa, com sequencia crescente. Registros anteriores nunca sao atualizados nem removidos por rotinas normais.

Em modo estudo, a correcao e devolvida apos a resposta. Em modo prova/simulado, o endpoint omite gabarito, comentario e dados que permitiriam inferi-lo ate a finalizacao.

## Estatisticas e analise

As estatisticas sao consultas agregadas sobre respostas finalizadas. Cache de leitura pode ser adicionado depois, mas nao sera fonte de verdade. Por padrao, cada questao vale uma tentativa final: para analise longitudinal, usa-se a ultima resposta de cada tentativa; para metricas de volume, contam-se todas as tentativas. A definicao aparece no payload de cada metrica.

`PerformanceAnalysisService` so classifica um assunto com pelo menos 10 respostas em pelo menos 3 dias distintos. O escore inicial de fragilidade combina: taxa de erro (45%), recencia de erros (20%), recorrencia na mesma questao/assunto (15%), desempenho em itens medio/dificeis (10%) e tendencia das ultimas 10 respostas (10%). O resultado inclui amostra e confianca. Menos dados geram "dados insuficientes", nunca julgamento negativo.

`StudyRecommendationService` ordena assuntos com confianca suficiente pelo escore de fragilidade, prioriza revisoes vencidas e limita a lista a 3-5 itens. A recomendacao explica fatos observaveis, por exemplo: percentual, quantidade, tendencia e erros recorrentes. Acoes iniciais: revisar teoria, responder conjunto filtrado ou iniciar revisao.

## IA e privacidade

`AIProvider` sera uma interface com operacoes de chat e geracao de questoes. `StudyContextBuilder` recebe usuario, intencao e escopo permitido, e devolve somente agregados e itens necessarios. Nenhum provider acessa repositorios diretamente.

Para uma questao em simulado ainda aberto, o contexto exclui gabarito, comentario editorial e explicacao. O prompt do assistente exige ajuda conceitual sem confirmar alternativa ou sugerir resposta. Questao gerada por IA nasce em `REVIEW`, com provider, modelo, prompt, data e assunto registrados, e requer publicacao administrativa.

## Seguranca e operacao

- Validacao por DTO antes dos casos de uso; Doctrine com parametros elimina SQL manual.
- HTML de enunciados e comentarios sera sanitizado em lista permitida; o frontend nunca renderiza HTML nao confiavel sem sanitizacao.
- CORS permitido apenas para origens configuradas; rate limiting para login, refresh e assistente.
- Logs JSON incluem request id, usuario quando disponivel e duracao, mas nunca senha, token, segredo ou prompt com dados sensiveis desnecessarios.
- `compose.yaml` usara redes internas para MySQL/Redis, volumes nomeados e healthchecks para MySQL, API, frontend e Nginx.

## Frontend modular

A SPA aplica os mesmos limites de contexto do backend. Cada modulo usa Domain, Application, Infrastructure e Interface/Http. O cliente Axios e um adaptador de infraestrutura unico: normaliza envelopes, erros, autenticacao e paginacao. Paginas e componentes nao conhecem detalhes HTTP ou regras de negocio.

## Transição para taxonomia canônica

O catálogo legado mantém `subjects` associado a `syllabi` para compatibilidade com o MVP. A partir do marco M0, o contexto **Taxonomy** passa a concentrar assuntos canônicos globais, aliases, prevenção de ciclos, propostas de duplicidade e fusões auditáveis. Nenhuma relação existente é removida nesta etapa: a associação entre edital/cargo e assunto canônico será adicionada no marco M1, com migração explícita e reprocessável.
