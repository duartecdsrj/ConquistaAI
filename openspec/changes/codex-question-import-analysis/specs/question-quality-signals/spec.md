## ADDED Requirements

### Requirement: Sinal editorial seguro de qualidade da questão
O sistema SHALL derivar um sinal de qualidade visível para questão somente a partir de achados persistidos de importação e SHALL manter o achado original imutável e auditável.

#### Scenario: Questão sem ressalva qualificada
- **WHEN** uma questão não possuir achado qualificado de incoerência
- **THEN** a API não retornará alerta editorial ao aluno

#### Scenario: Questão com possível incoerência
- **WHEN** existir achado qualificado de conflito de gabarito, imagem ou estrutura
- **THEN** a API retornará categoria segura e observação curta sem revelar alternativa, gabarito, justificativa ou confiança interna

### Requirement: Avisos claros em resolução e revisão
O sistema SHALL mostrar os sinais editoriais com dados reais na resolução de caderno e na revisão administrativa, respeitando a visibilidade apropriada a cada público.

#### Scenario: Resolução pelo aluno
- **WHEN** o aluno abrir uma questão com alerta editorial
- **THEN** a interface mostrará a observação diretamente, sem exigir expansão, e não oferecerá conteúdo que permita inferir a resposta

#### Scenario: Revisão administrativa de discrepância visual
- **WHEN** o administrador abrir uma questão com `IMAGE_DISCREPANCY`
- **THEN** a tela de revisão exibirá o achado, página/evidência permitida e situação visual diretamente no item
