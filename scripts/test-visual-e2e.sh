#!/bin/sh
set -eu

compose='docker compose -p concursos-e2e -f compose.yaml -f compose.e2e.yaml'
$compose down -v --remove-orphans
$compose up -d --build

ready=false
for attempt in $(seq 1 60); do
  if $compose exec -T nginx wget -qO- http://127.0.0.1/health >/dev/null 2>&1; then
    ready=true
    break
  fi
  sleep 1
done
if [ "$ready" != true ]; then
  $compose logs --no-color
  exit 1
fi
$compose exec -T frontend npm run test:visual
