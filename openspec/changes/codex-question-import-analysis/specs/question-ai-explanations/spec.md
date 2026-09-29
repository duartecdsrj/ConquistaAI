## MODIFIED Requirements

### Requirement: Explicação orientada à aprendizagem
O sistema SHALL retornar explicação que relacione conceito, alternativa correta e escolha do usuário quando permitidos, sem copiar a questão como flashcard, e SHALL considerar ressalvas editoriais persistidas para explicar a interpretação provável da banca sem apresentar suspeita como fato.

#### Scenario: Erro ou insegurança recorrente
- **WHEN** sinais recentes indicarem erro ou não domínio no mesmo conceito
- **THEN** a explicação identifica o tópico de reforço e produz saída estruturada reutilizável pela análise adaptativa

#### Scenario: Ressalva editorial após resposta
- **WHEN** o usuário solicitar explicação depois de concluir uma tentativa em questão com achado editorial qualificado
- **THEN** o contexto inclui a ressalva segura e a explicação pode relacionar a interpretação provável da banca à evidência disponível, distinguindo-a de confirmação oficial

#### Scenario: Ressalva editorial antes da resposta
- **WHEN** o usuário solicitar explicação antes de concluir uma tentativa em questão com achado editorial
- **THEN** a resposta preserva as restrições de segurança e não revela gabarito, alternativa correta ou argumento que permita inferi-los
