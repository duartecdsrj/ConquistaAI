## ADDED Requirements

### Requirement: Localização automática de evidências da questão
Quando houver PDF de origem disponível, o worker SHALL pesquisar no texto extraído o primeiro parágrafo não vazio do enunciado e uma alternativa não vazia central do snapshot, independentemente de a instrução solicitar uma busca. As páginas encontradas e a janela de duas páginas vizinhas SHALL ser unidas, sem duplicidade e em ordem numérica, às evidências de origem antes de renderizar imagens para o Codex.

#### Scenario: Solicitação sem comando de busca
- **WHEN** uma solicitação possui PDF de origem e a instrução não contém comando de localização
- **THEN** o worker procura as duas referências automáticas e envia as páginas encontradas, suas vizinhas e as páginas de origem como evidências visuais

#### Scenario: Uma referência não é localizada
- **WHEN** o PDF não contém uma das referências automáticas ou ela não possui texto útil
- **THEN** o worker continua a correção com as demais evidências disponíveis sem marcar a solicitação como falha por esse motivo

### Requirement: Observabilidade segura da localização automática
O worker SHALL registrar a localização automática com identificador da referência, hash do trecho, páginas encontradas e conjunto final de evidências, sem registrar o texto do enunciado ou da alternativa.

#### Scenario: Referência automática localizada
- **WHEN** a busca automática encontra uma ou mais páginas
- **THEN** o log estruturado contém somente o identificador da referência, seu hash e os números de páginas
