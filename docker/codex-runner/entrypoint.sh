#!/bin/sh
set -eu

# A autenticação do plano Pro é persistida somente no volume /root/.codex.
if [ -n "${OPENAI_API_KEY:-}" ]; then
  printf '%s' "$OPENAI_API_KEY" | codex login --with-api-key >/dev/null
  unset OPENAI_API_KEY
fi

if ! codex login status >/dev/null 2>&1; then
  echo "Sessão do Codex Pro não autorizada. Execute docker compose run --rm codex-auth." >&2
  exit 77
fi

exec codex "$@"
