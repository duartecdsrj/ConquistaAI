## 1. Contratos e persistência

- [ ] 1.1 Criar migration aditiva para análises imutáveis por questão, achados tipados, âncoras visuais, telemetria e agregados de job, preservando jobs existentes.
- [ ] 1.2 Modelar entidades, enums, value objects e interfaces de repositório para análise, achados, uso de tokens e projeção segura de qualidade.
- [ ] 1.3 Definir DTOs e schema versionado de comando/resposta do analisador, com validação de metadados, correlação/afirmações, evidências, gabarito e imagens.
- [ ] 1.4 Criar a porta `QuestionImportAnalyzerInterface` e factory configurável por ambiente, com provider indisponível seguro.

## 2. Analisador Codex e execução assíncrona

- [ ] 2.1 Preparar segmentação determinística de candidato, páginas de evidência e manifesto de imagens normalizadas por questão.
- [ ] 2.2 Implementar adaptador Codex isolado que use workspace efêmero, schema de saída e coleta segura de provider, modelo, duração e tokens reportados.
- [ ] 2.3 Atualizar o worker de importação para analisar cada questão antes da escrita, respeitar cancelamento, timeout, orçamento e retentativa persistida.
- [ ] 2.4 Criar adaptador de teste compatível e documentar a extensão para futura API externa sem alterar o caso de uso.

## 3. Escrita editorial e histórico

- [ ] 3.1 Atualizar o escritor Doctrine para validar a análise antes de criar questão, manter regras de sanitização, duplicidade, taxonomia, colunas e afirmações.
- [ ] 3.2 Persistir metadados somente com evidência explícita, associar ativos apenas por âncora válida e registrar discrepâncias visuais sem anexo automático ambíguo.
- [ ] 3.3 Persistir `ANSWER_KEY_CONFLICT` e demais achados sem publicar, corrigir ou alterar gabarito automaticamente.
- [ ] 3.4 Expor no histórico administrativo do job totais por achado, provider/modelo, duração e tokens reais ou indisponibilidade explícita.

## 4. Contratos HTTP e experiência integrada

- [ ] 4.1 Atualizar `docs/API.md`, DTOs, mappers e endpoints administrativos para o histórico detalhado de análise e telemetria.
- [ ] 4.2 Implementar módulo frontend administrativo DDD para histórico de importação e revisão direta de achados, incluindo discrepâncias visuais sem exigir expansão.
- [ ] 4.3 Estender respostas de questão e o módulo Study/Question Bank para mostrar aviso seguro, permanente e não expansível durante resolução e revisão pública.
- [ ] 4.4 Incorporar a ressalva editorial autorizada ao contexto de explicação pós-tentativa e preservar a não revelação antes da resposta.
- [ ] 4.5 Atualizar `docs/FRONTEND.md` com os estados de carregamento, falha, vazio, aviso editorial e indisponibilidade de telemetria.

## 5. Validação e operação

- [ ] 5.1 Cobrir unitariamente schemas, política de segurança, provider/factory, orçamento e regras de sinalização.
- [ ] 5.2 Cobrir integração Doctrine para imutabilidade, isolamento, telemetria, gabarito conflitante, metadados sem evidência e imagens ambíguas.
- [ ] 5.3 Cobrir controller/API e SPA para contratos, não revelação, banners diretos e revisão visual.
- [ ] 5.4 Configurar worker e variáveis de ambiente, validar Compose, migração idempotente, suíte focal e build da SPA.
