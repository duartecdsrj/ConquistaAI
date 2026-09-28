## Why

A resolução de cadernos em telas pequenas deixa os controles fora do alcance e mistura navegação, progresso e conteúdo na mesma rolagem. Os ícones de pausa e finalização também desaparecem ao compactar o cabeçalho, removendo ações essenciais durante o estudo.

## What Changes

- Organizar a execução de cadernos no celular em abas de Questão, Navegação e Progresso.
- Permitir alternar essas abas por toque e arrasto horizontal, sem conflitar com a rolagem vertical do enunciado.
- Manter as ações de resposta e navegação no rodapé visível enquanto somente o conteúdo da questão rola.
- Preservar os ícones e a ação de pausar/retomar no cabeçalho compacto.
- Cobrir o fluxo mobile e desktop com Playwright, incluindo visibilidade dos controles e interação por gesto.

## Capabilities

### New Capabilities

- `execucao-mobile-de-caderno`: experiência responsiva de resolução, navegação e controle de caderno em dispositivos móveis.

### Modified Capabilities

## Escopo ampliado

Além da experiência móvel, esta mudança passa a garantir continuidade da sessão e seleção útil de estudo: persistir a questão ativa, bloquear respostas enquanto pausado, ordenar a seleção congelada por assunto e excluir questões respondidas nos últimos 30 dias ou reservadas em cadernos não finalizados do mesmo usuário.

A implementação altera contrato HTTP, persistência e regras de tentativa apenas para esses comportamentos. Não altera correção, estatísticas ou a seleção de cadernos já criados.
