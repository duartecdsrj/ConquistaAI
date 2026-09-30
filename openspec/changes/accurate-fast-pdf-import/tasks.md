## 1. Diagnóstico e segmentação

- [x] 1.1 Adicionar regressão textual para os cinco PDFs, com 66 candidatos de múltipla escolha com gabarito.
- [x] 1.2 Delimitar candidatos pelo marcador de gabarito e alternativas, excluindo Certo/Errado sem chamada ao provider.

## 2. Execução resiliente

- [x] 2.1 Separar falhas determinísticas das falhas transitórias e impedir backoff para conteúdo inválido.
- [x] 2.2 Tratar deadlock de reivindicação sem fatal e sem fechar o EntityManager.

## 3. Histórico e validação

- [x] 3.1 Reconciliar contadores de rejeição e processamento do histórico.
- [ ] 3.2 Validar suite focal, Compose, build e importação dos cinco PDFs.
