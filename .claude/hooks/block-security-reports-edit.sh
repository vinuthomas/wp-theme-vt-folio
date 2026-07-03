#!/bin/bash
# PreToolUse hook: blocks Edit/Write to security-reports/*, which holds gitignored
# audit history (the only record of past manual security reviews). Prevents an
# accidental overwrite of past findings; the user can still edit these files directly
# outside Claude Code if truly intentional.
set -u

input=$(cat)
file=$(printf '%s' "$input" | jq -r '.tool_input.file_path // empty')

if [[ "$file" == *"/security-reports/"* ]]; then
  echo "Blocked: $file is under security-reports/, which stores gitignored past audit findings. If this edit is intentional, make it outside Claude Code." >&2
  exit 2
fi

exit 0
