## Por quê

O worker só amplia as evidências quando a instrução pede explicitamente uma busca, embora o enunciado e as alternativas já contenham referências suficientes para localizar a questão no PDF. Isso pode deixar a correção sem a página visual relevante e depende de uma formulação específica do usuário.

## O que muda

- Em toda solicitação com PDF de origem disponível, o worker pesquisará o primeiro parágrafo não vazio do enunciado e uma alternativa central não vazia no texto extraído do PDF, independentemente da instrução.
- As páginas encontradas e suas janelas de evidência passarão a integrar as imagens enviadas ao worker de correção, junto das evidências já existentes.
- A busca explícita por trecho e a busca de gabarito permanecem complementares ao novo comportamento.
- Logs estruturados registrarão apenas hashes e páginas dos trechos automáticos, sem conteúdo da questão.

## Capacidades

### Novas capacidades

Nenhuma.

### Capacidades modificadas

- `execucao-resiliente-do-worker-de-correcao`: a composição automática de evidências passa a pesquisar conteúdo do snapshot antes da execução do Codex.

## Impacto

Afeta o worker `apps/api/bin/process-question-corrections.php`, seus testes e a documentação do contrato de correção em `docs/API.md`. Não altera rotas, payloads, estados nem a aprovação administrativa.
