# Frontend

A interface web usa Vue 3 e Quasar, com foco em leitura rápida, navegação simples e boa adaptação a telas menores.

## Tela inicial

A página inicial inclui:

- menu lateral recolhível com acesso a Início, Estudar, Cadernos, Desempenho e Revisões;
- resumo de questões, acertos, tempo e revisões pendentes;
- meta semanal e sequência de estudo;
- cartões para retomar cadernos;
- desempenho por assunto.

Em telas de celular, o menu lateral pode ser aberto pelo botão no cabeçalho, os cartões passam a ocupar uma coluna e as ações principais usam toda a largura disponível.

## Design

A paleta utiliza fundo claro, azul como cor de ação principal e tons suaves para estados informativos. Os componentes são nativos do Quasar, sem imagens pesadas, para preservar carregamento leve.

## Execução

```bash
docker compose up -d --build
```

A interface fica disponível em `http://localhost:8081`.

## Validação

O comando configurado é:

```bash
docker compose exec frontend npm run build
```

O projeto ainda não possui `tsconfig.json`; por isso o `vue-tsc --noEmit` exibe a ajuda do TypeScript antes de chamar o Vite. A interface é servida em modo de desenvolvimento pelo Vite no Compose.
