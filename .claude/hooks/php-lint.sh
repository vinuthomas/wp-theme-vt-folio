#!/bin/bash
# PostToolUse hook: lints PHP files after Edit/Write via the local Docker WP container.
# There is no PHP binary on the host, so this shells out to the running `vt_wordpress`
# container (see /Users/vinuthomas/code/vinuthomas-local/docker-compose.yml), which
# mounts this theme at the same relative path. Silently no-ops if the container isn't up.
set -u

input=$(cat)
file=$(printf '%s' "$input" | jq -r '.tool_input.file_path // empty')

[[ -z "$file" || "$file" != *.php ]] && exit 0

container="vt_wordpress"
docker inspect -f '{{.State.Running}}' "$container" >/dev/null 2>&1 || exit 0

project_dir="${CLAUDE_PROJECT_DIR:-$PWD}"
rel="${file#"$project_dir"/}"
[[ "$rel" == "$file" ]] && exit 0

container_path="/var/www/html/wp-content/themes/vinu-thomas/$rel"
output=$(docker exec "$container" php -l "$container_path" 2>&1)
status=$?

if [[ $status -ne 0 ]]; then
  echo "php -l failed for $rel:" >&2
  echo "$output" >&2
  exit 2
fi

exit 0
