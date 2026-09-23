#!/usr/bin/env sh
set -eu
: "${MYSQL_DATABASE:?MYSQL_DATABASE is required}"
: "${MYSQL_USER:?MYSQL_USER is required}"
: "${MYSQL_PASSWORD:?MYSQL_PASSWORD is required}"
mkdir -p backups
docker compose exec -T mysql mysqldump --no-tablespaces -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" | gzip > "backups/concursos-$(date -u +%Y%m%dT%H%M%SZ).sql.gz"
