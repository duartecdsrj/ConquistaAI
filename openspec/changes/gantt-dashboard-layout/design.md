## Context

O mapa atual já persiste datas, estado e dependências por usuário e concurso, e calcula desempenho com respostas finais. A tela, porém, mostra apenas uma tabela Gantt básica. O desenho de referência requer um painel de planejamento: carga estimada, progresso, calendário e indicadores laterais. A estimativa de carga e a data de prova ainda não são persistidas pelo mapa.

## Goals / Non-Goals

**Goals:**

- Disponibilizar um painel Gantt com leitura comparável à referência em telas amplas e adaptação funcional em telas pequenas.
- Manter separadas as verdades de planejamento (datas, estimativa e estado escolhido pelo usuário) e de prática (respostas e acurácia).
- Calcular todos os indicadores a partir dos dados persistidos do usuário autenticado.

**Non-Goals:**

- Não inferir que um assunto foi concluído somente porque possui respostas de flashcard.
- Não criar horários diários, lembretes ou integração com calendários externos.
- Não alterar a taxonomia oficial do concurso para acomodar a visualização.

## Decisions

### Perfil de planejamento por usuário e concurso

Será criada uma configuração de mapa, identificada por usuário e concurso, para guardar a data da prova. A estimativa em minutos será guardada no próprio item agendado. Essa separação permite que usuários com a mesma prova tenham prazos e cargas individuais.

A alternativa de gravar a data diretamente em `exams` foi descartada: a data pode variar por edição, cargo ou plano pessoal. Inferir carga a partir da duração da faixa também foi descartado, pois dias de calendário não representam horas de estudo.

### Indicadores agregados no caso de uso do mapa

O caso de uso de consulta retornará um resumo de planejamento, distribuição por assunto de primeiro nível e indicadores de desempenho por assunto. Tempo concluído contará somente itens marcados como `COMPLETED`; itens `STUDIED` continuam visíveis e preservam posição, mas não são tratados como conclusão total. Acurácia continua derivada apenas das práticas concluídas no período solicitado.

Agregar no backend evita duplicação de regras, exposição de entidades e divergências entre desktop e celular. O frontend só formata valores e compõe a visualização.

### Composição da tela

A página de Cronograma manterá a seleção de concurso e consumirá um único mapa. O componente Gantt terá cartões de resumo, barra de navegação de intervalo, filtros, hierarquia e grade temporal no painel principal. Uma barra lateral apresentará progresso geral, distribuição da carga, forças e fraquezas e a legenda. Em celular, os blocos serão empilhados e a grade permanecerá rolável horizontalmente.

Será usado Quasar para campos, cartões, botões e estados da interface. O gráfico de distribuição será desenhado por CSS a partir da distribuição retornada; nenhuma dependência de gráficos será introduzida.

## Risks / Trade-offs

- [Planos existentes sem estimativa] → migrar com zero e sinalizar carga não informada, sem apresentar horas fictícias.
- [Sem práticas em um assunto] → mostrar estado sem evidência e não classificá-lo artificialmente como ponto forte ou fraco.
- [Grade extensa em telas pequenas] → manter controles acessíveis e rolagem horizontal limitada à grade.
- [Concurso sem data de prova] → exibir prazo indisponível e oferecer edição no fluxo de planejamento.

## Migration Plan

1. Adicionar campos/tabela de planejamento preservando os itens existentes.
2. Publicar o contrato expandido e seus testes antes da interface que o consome.
3. Publicar o painel com estados de carregamento, vazio e erro.
4. Em rollback, a interface ignora campos novos; os dados adicionais permanecem sem afetar o cronograma atual.

## Open Questions

Nenhuma. O valor inicialmente ausente de estimativa será explicitamente tratado como não informado, e não estimado pelo sistema.
