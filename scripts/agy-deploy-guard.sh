#!/usr/bin/env bash
# Antigravity PreToolUse Hook: Prompts user for confirmation before executing live SFTP deployments.
set -euo pipefail

INPUT=$(cat)

if echo "$INPUT" | grep -qE '(deploy-sftp\.sh|npm run deploy|sftp )'; then
  echo '{"decision": "ask", "reason": "Live SFTP deployment detected. Please confirm before uploading dist/ to the remote SFTP server."}'
else
  echo '{"decision": "allow"}'
fi
