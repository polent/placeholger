#!/usr/bin/env bash
# Local CLI SFTP Deployment Script for placeholger.hellinger.wtf
# Pure SFTP upload using OpenSSH sftp (or lftp sftp:// when SFTP_PASSWORD is set).
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

if [ ! -f ".env" ]; then
  echo "❌ Error: .env file not found. Copy .env.example to .env and fill in your SFTP credentials."
  exit 1
fi

set -a
# shellcheck disable=SC1091
source .env
set +a

: "${SFTP_HOST:?Missing SFTP_HOST in .env}"
: "${SFTP_USER:?Missing SFTP_USER in .env}"
SFTP_PORT="${SFTP_PORT:-22}"
SFTP_REMOTE_DIR="${SFTP_REMOTE_DIR:-./}"
SFTP_SSH_KEY="${SFTP_SSH_KEY:-}"
SFTP_PASSWORD="${SFTP_PASSWORD:-}"

echo "==> Running build and test suite before SFTP deployment..."
npm test

SFTP_OPTS=(-P "${SFTP_PORT}" -o StrictHostKeyChecking=accept-new)
if [ -n "$SFTP_SSH_KEY" ]; then
  EXPANDED_KEY="${SFTP_SSH_KEY/#\~/$HOME}"
  SFTP_OPTS+=(-i "${EXPANDED_KEY}")
fi

BATCH_FILE="$(mktemp)"
trap 'rm -f "$BATCH_FILE"' EXIT

cat > "$BATCH_FILE" <<EOF
-mkdir ${SFTP_REMOTE_DIR%/}
-mkdir ${SFTP_REMOTE_DIR%/}/assets
-mkdir ${SFTP_REMOTE_DIR%/}/views
-mkdir ${SFTP_REMOTE_DIR%/}/.well-known
put -r dist/. ${SFTP_REMOTE_DIR%/}/
bye
EOF

echo "==> Uploading ./dist/ to sftp://${SFTP_USER}@${SFTP_HOST}:${SFTP_PORT}/${SFTP_REMOTE_DIR#/}..."

if [ -z "$SFTP_SSH_KEY" ] && [ -n "$SFTP_PASSWORD" ]; then
  if command -v sshpass >/dev/null 2>&1; then
    sshpass -p "$SFTP_PASSWORD" sftp "${SFTP_OPTS[@]}" -b "$BATCH_FILE" "${SFTP_USER}@${SFTP_HOST}"
  elif command -v lftp >/dev/null 2>&1; then
    lftp -u "${SFTP_USER},${SFTP_PASSWORD}" -p "${SFTP_PORT}" "sftp://${SFTP_HOST}" \
      -e "set sftp:auto-confirm yes; mirror --reverse --delete --verbose ./dist/ ${SFTP_REMOTE_DIR}; bye"
  else
    echo "ℹ️  Prompting for SFTP password interactively (install sshpass for non-interactive password auth, or use SFTP_SSH_KEY):"
    sftp "${SFTP_OPTS[@]}" -b "$BATCH_FILE" "${SFTP_USER}@${SFTP_HOST}"
  fi
else
  sftp "${SFTP_OPTS[@]}" -b "$BATCH_FILE" "${SFTP_USER}@${SFTP_HOST}"
fi

echo "✅ SFTP deployment complete!"
