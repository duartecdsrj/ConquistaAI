## Context

A página de execução já consome os casos de uso de Study e não exige novos dados. Em telas até 599 px, o CSS atual empilha painéis e reduz botões do cabeçalho, mas esconde ícones junto com os rótulos e deixa a ação principal após todo o conteúdo.

## Goals / Non-Goals

**Goals:**

- Disponibilizar Questão, Navegação e Progresso como vistas móveis acessíveis por abas e gesto horizontal.
- Isolar a rolagem vertical do conteúdo da questão e fixar o rodapé de controles dentro da vista.
- Preservar controles do cabeçalho com ícones, rótulos acessíveis e alvos de toque adequados.
- Validar os comportamentos nos projetos Playwright desktop e mobile existentes.

**Non-Goals:**

- Alterar APIs, persistência, regras de tentativa ou o comportamento desktop.
- Incluir novas funcionalidades de estatísticas ou marcação.

## Decisions

- A página mantém a mesma fonte de estado (`useNotebookExecution`); as abas são estado local de apresentação. Isso evita alterar o contrato de Study para uma mudança de layout.
- No mobile, um contêiner de vistas trata arrasto predominantemente horizontal como troca de aba. Rolagem vertical continua no painel da questão, evitando captura de gesto de leitura.
- O rodapé de ações fica fora do contêiner rolável e a área de leitura usa flexbox com `min-height: 0`. Assim o controle é sempre visível sem `position: fixed` global, que conflitaria com diálogos e a área segura do dispositivo.
- Os rótulos compactos são ocultados seletivamente, nunca por `font-size: 0` no conteúdo inteiro do botão; ícones Material do Quasar permanecem renderizados.
- A questão ativa é estado de domínio do caderno: a troca de questão envia um comando tipado ao backend; ao carregar, a resposta do caderno informa a questão ativa para o composable posicionar a seleção congelada.
- O serviço de tentativa e o de resposta consultam o caderno do proprietário dentro da transação e rejeitam estado diferente de `IN_PROGRESS`; assim uma chamada direta não contorna a pausa.
- A seleção usa consultas Doctrine parametrizadas para excluir IDs respondidos recentemente e IDs congelados em cadernos abertos do mesmo usuário. Depois de selecionar, a ordem por assunto é persistida em `notebook_questions` e nunca recalculada.

## Risks / Trade-offs

## Continuidade e seleção

A posição ativa será persistida no caderno por comando autenticado e retornada na leitura; o composable converte o ID para o índice da seleção congelada. Tentativas e respostas verificam transacionalmente que o caderno pertence ao usuário e está `IN_PROGRESS`. A seleção nova exclui respostas finais dos últimos 30 dias e IDs reservados por cadernos do mesmo usuário em `DRAFT`, `IN_PROGRESS` ou `PAUSED`; se não alcançar a quantidade, falha com 422. Antes de persistir, a lista é ordenada deterministicamente por assunto canônico, de modo que a navegação entre assuntos opere sobre blocos consecutivos.
