## Context

Os cinco PDFs de referência contêm 66 marcadores explícitos `Gabarito: Letra <A-E>` (DevOps 2, Git 25, Linux 2, Python 15 e XML/JSON/CSV 22). O segmentador atual depende apenas do início numerado e o worker reenfileira lotes por falhas que não são transitórias; quatro consumidores também disputam o mesmo índice de checkpoints com locks pessimistas.

## Goals / Non-Goals

**Goals:**

- Formar candidatos de múltipla escolha delimitados pelo marcador de gabarito explícito e pelas alternativas A-E.
- Rejeitar itens Certo/Errado antes do provider e persistir motivo seguro.
- Evitar que falhas de validação e deadlocks deixem o job em espera exponencial desnecessária.
- Limitar o trabalho concorrente aos candidatos realmente importáveis e expor totais coerentes.

**Non-Goals:**

- Importar itens Certo/Errado nesta mudança.
- Substituir a análise editorial do Codex ou inferir gabaritos sem marcador explícito.

## Decisions

- O pré-segmentador tratará `Gabarito: Letra A-E` como delimitador terminal, preservando o texto desde o início numerado mais próximo e validando quatro ou cinco alternativas antes de criar checkpoint. Isso é mais confiável que inferir limites exclusivamente pela próxima numeração.
- Itens sem alternativas A-E ou com `Gabarito: Certo/Errado/Correto/Errado` serão contabilizados como excluídos determinísticos, sem chamar o provider nem consumir retentativa.
- O repositório reivindicará checkpoints com tentativa curta para deadlock e retornará vazio de modo recuperável; a falha de lock não poderá fechar o EntityManager nem consumir retry do candidato.
- A retentativa ficará restrita a falhas externas do provider. Resposta inválida, ausência estrutural e erro de validação encerram somente o candidato com motivo seguro.

## Risks / Trade-offs

- [PDF sem marcador de gabarito] → permanece fora desta importação para não inventar resposta.
- [Variações tipográficas] → padrões serão cobertos pelos cinco PDFs e mantidos extensíveis.
- [Menor número de chamadas] → o lote continua limitado por payload para evitar respostas truncadas.

## Migration Plan

Não exige migração. Jobs novos usam a segmentação; jobs em andamento podem ser cancelados e reenfileirados explicitamente após a implantação.
