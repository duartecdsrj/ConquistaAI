## 1. Notificações em tempo real

- [x] 1.1 Criar persistência, DTOs, serviços e rotas internas de notificação de correção.
- [x] 1.2 Criar gateway Socket.IO autenticado por JWT e adicioná-lo ao Compose/Nginx.
- [x] 1.3 Publicar eventos do worker e recuperar notificações pendentes pela SPA.

## 1. Worker resiliente

- [x] 1.4 Adicionar logs estruturados, timeout configurável, encerramento seguro e limpeza garantida.
- [x] 1.5 Configurar execução não interativa do Codex limitada ao sandbox da questão.
- [x] 1.6 Incluir janela de evidência PDF de duas páginas antes/depois para localizar imagens.
- [x] 1.7 Inicializar a sessão não interativa do Codex no executor efêmero, sem expor a chave ao workspace.
- [x] 1.8 Informar indisponibilidade de cota do provedor sem expor detalhes internos.
- [x] 1.9 Permitir executor autenticado pelo Codex Pro em volume dedicado e fluxo de login por dispositivo.
- [x] 1.10 Registrar metadados não sensíveis da proposta antes da validação estrutural.
- [x] 1.11 Completar alternativas vazias com o snapshot original, preservando IDs, ordem e conteúdo.
- [x] 1.12 Entregar snapshot no prompt para evitar leitura de arquivo bloqueada pelo sandbox.
- [x] 2.4 Exibir prévia da questão proposta e permitir reenvio com sugestões no diálogo global.
- [x] 2.5 Permitir reenvio de solicitação que falhou, mantendo a aprovação restrita a proposta válida.

## 2. Interface global e correção

- [x] 2.1 Criar cliente WebSocket na infraestrutura e banner global com atalho para proposta.
- [x] 2.2 Disponibilizar correção assistida também no Banco de Questões.
- [x] 2.3 Atualizar diálogo administrativo para abrir a proposta notificada e aprová-la.

## 3. Navegação

- [x] 3.1 Sincronizar seção ativa e caderno aberto com URL e restaurar após atualização.
- [x] 3.2 Permitir próxima questão sem resposta e adicionar assunto anterior/próximo no Caderno.
- [x] 3.3 Recarregar no Caderno o conteúdo editorial aprovado, preservando posição e respostas.
- [x] 3.4 Converter a página visual aprovada em ativo autenticado e rejeitar referências Markdown internas.
- [x] 3.5 Suportar múltiplas figuras ordenadas, com extração e prévia individuais no marcador correto do enunciado.

## 4. Verificação

- [x] 4.1 Cobrir backend, gateway e SPA com testes proporcionais; validar reconexão e isolamento por usuário.
- [x] 4.2 Atualizar documentação operacional e estado de desenvolvimento.
