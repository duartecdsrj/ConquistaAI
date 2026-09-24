# 🎓 ConquistaAI

**Inteligência para a sua próxima conquista.**

O **ConquistaAI** é uma plataforma inteligente de preparação para concursos, desenvolvida para transformar editais, questões e resultados em um plano de estudos personalizado.

A plataforma utiliza Inteligência Artificial para interpretar editais, identificar cargos e conteúdos programáticos, organizar automaticamente os assuntos em uma estrutura hierárquica de conhecimento e relacioná-los às questões disponíveis no banco.

A partir do histórico de resoluções, o ConquistaAI acompanha a evolução do usuário por disciplina e assunto, identifica pontos fortes, dificuldades e conteúdos ainda não estudados, oferecendo recomendações sobre onde concentrar os estudos.

> A Fase 1 está em desenvolvimento. As funcionalidades já disponíveis e as planejadas por marco estão detalhadas no [cronograma](docs/DEVELOPMENT_SCHEDULE.md).

## Principais funcionalidades

- 📄 Importação e análise inteligente de editais em PDF
- 🎯 Organização de concursos, cargos e conteúdos programáticos
- 🌳 Taxonomia hierárquica de assuntos
- 🧠 Classificação e deduplicação de assuntos com IA
- 📝 Banco de questões de concursos
- 🔎 Identificação e prevenção de questões duplicadas
- 📚 Criação de cadernos de questões e simulados
- 📊 Estatísticas detalhadas de desempenho
- 📈 Acompanhamento da evolução por assunto
- 🎯 Identificação de pontos fortes e pontos a melhorar
- 🔄 Controle de revisões e questões erradas
- 🤖 Assistente inteligente de estudos
- 🗓️ Recomendações e planos de estudo personalizados
- 🔍 Busca e catalogação de provas e gabaritos públicos
- 👥 Suporte a múltiplos usuários

## Organização do conhecimento

Os conteúdos são organizados hierarquicamente, permitindo acompanhar o desempenho desde uma disciplina até assuntos específicos.

```text
Informática
└── Redes de Computadores
    ├── TCP/IP
    ├── Roteamento
    └── Protocolos
        ├── DNS
        ├── DHCP
        ├── HTTP
        └── HTTPS
```

Essa estrutura permite que o ConquistaAI identifique exatamente quais partes do conteúdo programático precisam de maior atenção.

## Inteligência aplicada aos estudos

O objetivo do ConquistaAI não é apenas informar quantas questões foram acertadas ou erradas.

A plataforma cruza informações como:

- conteúdo exigido pelo edital;
- cargo escolhido;
- questões respondidas;
- taxa de acertos;
- erros recorrentes;
- tempo de resolução;
- recência dos estudos;
- evolução do desempenho;
- assuntos ainda não praticados;
- proximidade da prova;
- histórico de incidência de assuntos, quando disponível.

Com essas informações, o assistente pode responder perguntas como:

> **"O que devo estudar agora?"**

> **"Quais são meus principais pontos fracos?"**

> **"Quais assuntos do edital ainda não estudei?"**

> **"O que devo revisar esta semana?"**

> **"Em quais conteúdos meu desempenho está melhorando?"**

## Objetivo

O ConquistaAI busca transformar o estudo para concursos em um processo orientado por dados, utilizando IA para ajudar o candidato a decidir **o que estudar, quando revisar e onde concentrar seu tempo**.

## Documentação

- [Arquitetura](docs/ARCHITECTURE.md)
- [Modelo de dados](docs/DATABASE.md)
- [API REST](docs/API.md)
- [Roadmap](docs/ROADMAP.md)
- [Frontend](docs/FRONTEND.md)
- [Cronograma de desenvolvimento](docs/DEVELOPMENT_SCHEDULE.md)
- [Operação externa](docs/OPERATIONS.md)

## Princípios

- Monolito modular, com regras de negócio fora de controllers e componentes visuais.
- Histórico de tentativas imutável; estatísticas são calculadas a partir dele.
- Dados acadêmicos estritamente isolados por usuário.
- IA desacoplada por interfaces e com contexto mínimo necessário.
- Segurança adequada para exposição na internet, sem dependências operacionais desnecessárias.

## Stack

| Camada | Tecnologia |
| --- | --- |
| Frontend | Vue 3, Quasar, Composition API, TypeScript |
| API | PHP 8.3+, Slim 4, Doctrine ORM |
| Banco | MySQL 8 |
| Cache e proteção | Redis (rate limiting e refresh-token, opcional no primeiro corte) |
| Borda | Nginx |
| Execução | Docker Compose |

## Execução local

```bash
cp .env.example .env
HTTP_PORT=8081 docker compose up -d --build
```

Somente o Nginx terá porta pública. MySQL e Redis permanecem na rede interna do Compose.

## Backup e restauração

```bash
./scripts/backup.sh
./scripts/restore.sh backups/arquivo.sql.gz
```

Os scripts usam o serviço MySQL interno. Para criar o primeiro usuário local, use o comando abaixo com uma senha de ao menos 12 caracteres:

```bash
docker compose exec api php bin/create-user.php admin@example.test "Administrador" "uma-senha-segura" ADMIN,USER
```

---

**ConquistaAI** — *Inteligência para a sua próxima conquista.*
