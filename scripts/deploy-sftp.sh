#!/usr/bin/env bash
# Local CLI SFTP Deployment Script for placeholger.hellinger.wtf
# Pure SFTP mirror using lftp (--env-password) over sftp://.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

if [ ! -f ".env" ]; then
  echo "❌ Error: .env file not found. Copy .env.example to .env and fill in your SFTP credentials."
  exit 1
fi

if ! command -v lftp >/dev/null 2>&1; then
  echo "❌ Error: lftp is required for SFTP deployment. Install it with: sudo apt-get install -y lftp"
  exit 1
fi

while IFS= read -r line || [ -n "$line" ]; do
  line="${line%$'\r'}"
  case "$line" in
    ''|\#*) continue ;;
  esac
  key="${line%%=*}"
  val="${line#*=}"
  if [[ "$val" =~ ^\"(.*)\"$ ]] || [[ "$val" =~ ^\'(.*)\'$ ]]; then
    val="${BASH_REMATCH[1]}"
  fi
  export "$key=$val"
done < .env

: "${SFTP_HOST:?Missing SFTP_HOST in .env}"
: "${SFTP_USER:?Missing SFTP_USER in .env}"
: "${SFTP_PASSWORD:?Missing SFTP_PASSWORD in .env}"
SFTP_PORT="${SFTP_PORT:-22}"
SFTP_REMOTE_DIR="${SFTP_REMOTE_DIR:-/}"

echo "==> Running build and test suite before SFTP deployment..."
npm test

echo "==> Mirroring ./dist/ to sftp://${SFTP_USER}@${SFTP_HOST}:${SFTP_PORT}${SFTP_REMOTE_DIR}..."

export LFTP_PASSWORD="$SFTP_PASSWORD"
MIRROR="mirror --reverse --delete --parallel=4 --verbose"
MIRROR="$MIRROR --exclude-glob .htpasswd --exclude ^\.well-known/acme-challenge/"

lftp --env-password -u "$SFTP_USER" -p "$SFTP_PORT" \
  -e "set sftp:auto-confirm yes; set net:max-retries 3; $MIRROR ./dist/ $SFTP_REMOTE_DIR; bye" \
  "sftp://$SFTP_HOST"

echo "✅ SFTP deployment complete!"
