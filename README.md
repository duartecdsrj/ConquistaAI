# Plataforma de Estudos para Concursos

Aplicacao web auto-hospedada para estudo por questoes, inicialmente voltada ao concurso Transpetro - Analise de Sistemas: Infraestrutura, mas modelada para suportar qualquer concurso, edital e cargo.

O projeto ainda esta na etapa de arquitetura da Fase 1. A implementacao comeca depois da aprovacao desta base documental.

## Documentacao

- [Arquitetura](docs/ARCHITECTURE.md)
- [Modelo de dados](docs/DATABASE.md)
- [API REST](docs/API.md)
- [Roadmap](docs/ROADMAP.md)

## Principios

- Monolito modular, com regras de negocio fora de controllers e componentes visuais.
- Historico de tentativas imutavel; estatisticas sao calculadas a partir dele.
- Dados academicos estritamente isolados por usuario.
- IA desacoplada por interfaces e com contexto minimo necessario.
- Seguranca adequada para exposicao na internet, sem dependencias operacionais desnecessarias.

## Stack planejada

| Camada | Tecnologia |
| --- | --- |
| Frontend | Vue 3, Quasar, Composition API, TypeScript |
| API | PHP 8.3+, Slim 4, Doctrine ORM |
| Banco | MySQL 8 |
| Cache e protecao | Redis (rate limiting e refresh-token, opcional no primeiro corte) |
| Borda | Nginx |
| Execucao | Docker Compose |

## Execucao futura

Depois da Fase 1 ser implementada, a aplicacao sera iniciada com:

```bash
cp .env.example .env
HTTP_PORT=8081 docker compose up -d --build
```

Somente o Nginx tera porta publica. MySQL e Redis permanecerao na rede interna do Compose.

## Backup e restauracao planejados

```bash
./scripts/backup.sh
./scripts/restore.sh backups/arquivo.sql.gz
```

Os scripts validarao variaveis de ambiente e usarao o servico MySQL interno; detalhes serao incluidos junto com a infraestrutura na Fase 1.
