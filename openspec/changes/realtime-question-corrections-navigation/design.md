## Contexto

A API Slim e o worker são processos separados; a correção é persistida e assíncrona. A SPA não possui roteador de URL nem canal de eventos.

## Objetivos / Não objetivos

**Objetivos:** notificar o dono da correção em qualquer tela, manter a seção ao recarregar e permitir navegação livre no caderno.

**Não objetivos:** substituir REST, expor proposta a outro usuário, mudar gabarito automaticamente ou criar chat em tempo real.

## Decisões

- Gateway WebSocket Node separado, em rede interna, com Socket.IO e JWT de acesso. Motivo: conexões persistentes não cabem no processo PHP atual; Socket.IO trata reconexão. Alternativa SSE rejeitada pois a evolução para eventos bidirecionais e reconexão seria mais limitada.
- A API persiste notificações de correção e o worker publica evento por endpoint interno autenticado. O gateway entrega apenas ao `userId` alvo. A persistência permite recuperação após desconexão.
- A SPA mantém `section` e `notebook` em `URLSearchParams`; ao restaurar, valida a seção permitida e usa o fallback atual.
- O Caderno usa a lista congelada existente; próxima/anterior questão não depende de resposta. Assunto anterior/próximo procura o primeiro vínculo canônico diferente.
- A seleção congelada preserva somente os IDs e a ordem. Após aprovação editorial, a SPA consulta novamente as questões do Caderno, mantém a questão atual pelo ID e conserva respostas já registradas.
- A proposta pode declarar `asset_page` somente dentro da janela de evidência. Na aprovação, a infraestrutura renderiza essa página do PDF de origem em PNG, cria `QuestionAssetRecord` e a disponibiliza pela rota autenticada existente; não persiste caminhos Markdown internos.

## Riscos / compensações

- [Gateway indisponível] → REST continua permitindo consultar e aprovar; notificações ficam pendentes.
- [Token expirado] → gateway encerra conexão e cliente reconecta após renovação da sessão.
- [Evento duplicado] → cliente deduplica pelo ID da notificação/solicitação.
- [Mais um processo] → Compose inclui healthcheck e rede interna.

## Migração

Criar tabelas de notificação e serviço gateway; implantar API/worker/gateway juntos. Rollback para o commit anterior preserva solicitações e ignora eventos pendentes.

## Questões em aberto

Nenhuma para o primeiro corte: notificar apenas alterações de correção.

## Decisão adicional: execução resiliente do worker

- Cada fase (`claimed`, `pdf_rendered`, `codex_started`, `proposed`, `failed`, `timed_out`, `cleaned`) gera log estruturado com ID da solicitação, duração e resultado; nunca registra chave, prompt integral ou conteúdo da questão.
- O subprocesso Codex terá limite configurável, inicialmente 180 segundos. Ao exceder, o worker encerra o processo, remove os temporários e marca a solicitação `FAILED` com mensagem segura; a fila continua.
- A chamada será não interativa e pré-aprovada somente dentro do diretório temporário da questão, com sandbox somente leitura. A ausência de interação não amplia acesso a repositório, PDF completo ou credenciais.
- Para questões com fonte PDF, o conjunto de evidências inclui cada página de origem e uma janela de duas páginas anteriores e posteriores (limitada a páginas válidas), permitindo localizar figuras que cruzem a paginação sem entregar o documento completo.
- O executor efêmero autentica o Codex CLI antes da execução e remove `OPENAI_API_KEY` do ambiente do agente. A sessão de autenticação existe somente no sistema de arquivos descartado do contêiner, fora do volume que contém o snapshot e as imagens.
- Quando selecionado pelo administrador, o executor usa uma sessão autenticada do Codex Pro em volume dedicado, montado fora do workspace. Um serviço de autenticação por dispositivo inicializa essa sessão uma única vez; a chave da API não é injetada no executor.
- Falhas de cota do provedor são persistidas com orientação segura e específica; demais falhas continuam com mensagem genérica, sem expor logs, prompts ou credenciais.
- Antes da validação editorial, o worker registra exclusivamente metadados de forma da proposta (tipos e quantidade de alternativas), para diagnosticar rejeições sem registrar seu conteúdo.
- Quando a resposta estruturada declarar uma lista de alternativas vazia, o worker a completa a partir do snapshot imutável original antes da validação. Isso representa ausência de alteração nas alternativas e não cria ou modifica conteúdo.
- O snapshot e a instrução seguem diretamente no prompt do executor; o agente não precisa abrir arquivos para conhecer a questão. Os únicos anexos visuais permanecem as páginas de evidência do PDF.
- O diálogo global de proposta renderiza enunciado e alternativas reais antes de aprovar. O reenvio cria uma nova solicitação com a instrução anterior e as sugestões do administrador, preservando o histórico.
