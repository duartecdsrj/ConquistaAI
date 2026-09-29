## Context

O worker atual extrai texto do PDF, agrupa páginas candidatas e usa `AssistantProviderInterface` para devolver um JSON de extração. O escritor cria questões `REVIEW`, metadados, taxonomia e ativos; o job guarda apenas contadores agregados. A associação visual atual depende de referências e de fluxo determinístico, mas não mantém um veredito por questão sobre gabarito, metadados ou posição de imagem.

O produto já possui runner Codex isolado para correção e explicação, contratos de provider configuráveis e renderização de figuras por marcadores. A mudança precisa preservar o histórico, a revisão administrativa, a regra de não antecipar resposta em cadernos e a estrutura de correlação/afirmações.

## Goals / Non-Goals

**Goals:**

- Analisar cada questão candidata com provider estruturado antes de qualquer persistência editorial.
- Usar Codex inicialmente em worker isolado, sem acoplar o caso de uso ao CLI, e permitir outro provider por configuração.
- Guardar evidência, achados, posição de figuras e telemetria por questão e agregada por job.
- Expor alertas compreensíveis sem revelar gabarito ao aluno antes de concluir a tentativa.
- Conservar as validações de importação, metadados explicitamente evidenciados, correlação de colunas e afirmações.

**Non-Goals:**

- Publicar automaticamente uma questão, trocar automaticamente seu gabarito ou criar concurso/cargo a partir de inferência não evidenciada.
- Usar o aviso de dúvida como confirmação de erro editorial ou como justificativa para alterar o resultado de uma tentativa.
- Reprocessar retroativamente todos os PDFs já concluídos nesta primeira entrega.
- Expor prompt, token de acesso, caminhos internos, resposta bruta do provider ou alternativa correta antes da tentativa permitida.

## Decisions

### Porta exclusiva de análise de importação

Será criada `QuestionImportAnalyzerInterface`, com comando contendo texto segmentado, manifesto de imagens, páginas de evidência e taxonomia permitida; a resposta terá schema versionado. Ela é distinta de `AssistantProviderInterface`, pois exige saída determinística, validação editorial e telemetria de importação. O provider será escolhido por `QUESTION_IMPORT_ANALYZER`, com `codex` inicial e adaptadores futuros registrados pela mesma factory. Um provider indisponível falha/reagenda o job; não haverá fallback silencioso para o extrator antigo.

Alternativa descartada: ampliar `AssistantProviderInterface`. Isso misturaria conversa/RAG com importação transacional e não permitiria contrato de custo e veredito por questão.

### Codex como analisador isolado e orientado por artefatos

O adaptador Codex escreverá apenas `input.json`, imagens normalizadas/manifesto e `schema.json` em workspace temporário, executará o runner sem montar o banco ou documentos fora do escopo e consumirá somente `result.json` validado. O prompt exige análise de uma questão por vez, preservação literal de enunciado/alternativas, metadados com páginas de prova e classificação explícita de correlação/afirmações, gabarito e imagens.

O PDF continua sendo convertido para texto e imagens por componentes determinísticos. O modelo não recebe acesso livre ao PDF, à rede nem a outros dados do sistema.

### Persistência em duas camadas e bloqueio editorial

Cada resposta válida gera um `question_import_analysis` imutável ligado ao job e, quando a questão for criada, à questão. O registro armazena versão do schema/algoritmo, páginas, metadados extraídos, âncoras de imagem, achados tipados, confiança, justificativa segura, provider, modelo, duração, tokens de entrada/saída/total e custo quando retornados pelo provider. O job soma métricas e expõe totais no histórico.

O escritor só cria a questão após validar a análise contra regras estruturais existentes. Sinais `ANSWER_KEY_CONFLICT`, `IMAGE_DISCREPANCY` ou metadado insuficiente não publicam nem corrigem conteúdo automaticamente: mantêm a questão em `REVIEW` e criam achados visíveis à revisão. A associação de ativo exige âncora válida em enunciado ou alternativa; associações ambíguas não são anexadas.

Alternativa descartada: gravar somente um JSON no job. Isso não permitiria auditoria, alerta público seletivo ou revisão por questão.

### Sinal editorial seguro como projeção de achados

Uma projeção de qualidade da questão é derivada exclusivamente de achados persistidos e publicada na resposta de questão como categoria segura e texto curto, sem alternativa, gabarito, raciocínio nem nível de confiança. A revisão editorial renderiza todos os achados, inclusive discrepância visual, diretamente no cartão. A resolução mostra o aviso sem área expansível e mantém o comportamento normal de correção.

Após uma tentativa concluída, o contexto de explicação recebe os achados relevantes e pode explicar a interpretação provável da banca e a limitação da evidência. Antes disso, o provider recebe apenas um indicador de que há ressalva, sem dados que revelem resposta.

### Medição de tokens sem estimativa inventada

O contrato do provider exige contagens separadas de entrada e saída quando disponíveis. O total é a soma ou valor reportado; se o runner não informar uso confiável, os campos ficam nulos e o histórico declara `usageAvailability=UNAVAILABLE`. A interface administrativa sempre mostra a quantidade exata reportada ou a indisponibilidade, nunca uma estimativa apresentada como consumo real.

## Risks / Trade-offs

- [Análise por questão aumenta latência e custo] → fila persistida, lotes pequenos, orçamento configurável por job, cancelamento e telemetria agregada.
- [Modelo pode alucinar metadados ou resposta] → metadados precisam de página/evidência explícita; achados não alteram automaticamente conteúdo ou gabarito.
- [Figura extraída pode não corresponder à questão] → manifesto por página, âncora obrigatória, estado de discrepância e nenhum anexo automático quando ambíguo.
- [Codex pode não devolver tokens] → persistir indisponibilidade explícita e manter interface provider para APIs que retornem uso.
- [Aviso pode induzir aluno a desconfiar de toda questão] → redação neutra, sem resposta, somente quando achado qualificado existir e explicação restrita ao momento seguro.

## Migration Plan

1. Adicionar tabelas/colunas aditivas para análises, achados, telemetria e projeção segura; manter jobs e questões existentes compatíveis com valores nulos.
2. Registrar provider/factory, schema e worker Codex sem alterar o endpoint de envio de PDF.
3. Passar novos jobs pelo analisador e escritor validado; jobs legados permanecem consultáveis com telemetria indisponível.
4. Entregar contratos e SPA administrativa/resolução juntos, com feature flag de provider e orçamento.
5. Em falha, selecionar provider indisponível/legado somente para impedir novos processamentos; não apagar análises nem alterar questões já criadas.

## Open Questions

- Qual orçamento padrão por PDF e qual comportamento ao atingi-lo: interromper o job inteiro ou concluir apenas questões já analisadas?
- A primeira versão deve permitir administrador reanalisar uma única questão sem reenfileirar o PDF completo?
- Qual adaptação de provider HTTP será priorizada após Codex: Responses API ou outro serviço compatível com schema?
