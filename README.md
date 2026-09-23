# Plataforma de Estudos para Concursos

Aplicacao web auto-hospedada para estudo por questoes, inicialmente voltada ao concurso Transpetro - Analise de Sistemas: Infraestrutura, mas modelada para suportar qualquer concurso, edital e cargo.

A Fase 1 esta em implementacao. A infraestrutura e os fluxos iniciais de identidade ja podem ser executados localmente.

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

## Execucao local

Para iniciar a aplicacao:

```bash
cp .env.example .env
HTTP_PORT=8081 docker compose up -d --build
```

Somente o Nginx tera porta publica. MySQL e Redis permanecerao na rede interna do Compose.

## Backup e restauracao

```bash
./scripts/backup.sh
./scripts/restore.sh backups/arquivo.sql.gz
```

Os scripts usam o servico MySQL interno. Para criar o primeiro usuario local, use o comando abaixo com uma senha de ao menos 12 caracteres:

```bash
docker compose exec api php bin/create-user.php admin@example.test "Administrador" "uma-senha-segura" ADMIN,USER
```
