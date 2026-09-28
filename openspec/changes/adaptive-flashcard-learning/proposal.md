## Why

O desempenho em cadernos hoje termina em estatísticas e recomendações por assunto, sem transformar evidências de aprendizagem em revisões personalizadas. A ConquistaAI precisa fechar esse ciclo com reforço adaptativo, reutilizando a taxonomia e a correção assistida por IA já existentes sem gerar cartões ou conceitos duplicados.

## What Changes

- Criar o contexto Review para cartões compartilháveis, progresso individual, histórico imutável e agenda de repetição espaçada substituível.
- Relacionar questões e flashcards aos assuntos canônicos da Taxonomy, tratando-os como conceitos de conhecimento e preservando aliases/fusões existentes.
- Disparar, após a finalização idempotente de um caderno, análise assíncrona estruturada por IA no mesmo padrão de provider/job do corretor de questões.
- Aplicar somente ações de IA validadas: identificar lacunas, criar ou reutilizar cartões, antecipar revisões e ajustar sinais de domínio, com auditoria sem cadeia de raciocínio.
- Disponibilizar API e SPA para sessão diária, revisão rápida, classificação da recordação, resultado da análise do caderno e mapa de domínio por hierarquia.

## Capabilities

### New Capabilities

- `revisao-adaptativa-de-flashcards`: mantém cartões, progresso individual, revisões imutáveis, repetição espaçada e prioridade pedagógica.
- `analise-inteligente-de-caderno`: processa cadernos concluídos de forma assíncrona, idempotente e auditável para transformar desempenho em ações de revisão.
- `experiencia-de-revisao-e-dominio`: entrega sessão diária e rápida de flashcards, resultado de análise e mapa real de domínio na SPA.

### Modified Capabilities

- Nenhuma.

## Impact

- Backend: novos contratos, entidades, repositórios Doctrine, migration, serviços, DTOs, mappers e rotas do contexto Review; integração pontual com Study, Performance, Taxonomy, QuestionBank e Assistant.
- Processamento: worker e provider estruturado para análise de caderno, reutilizando a configuração, telemetria e entrega em tempo real do corretor de questões quando aplicável.
- Frontend: módulo DDD Review, repositório Axios, composable, rotas e componentes Quasar responsivos.
- Documentação: contratos em `docs/API.md`, fluxo SPA em `docs/FRONTEND.md`, decisões e validações em `docs/DEVELOPMENT_STATE.md`.
