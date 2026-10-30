# Regenerates values.schema.json and charts/larakube/README.md from values.yaml.
# Uses the standalone helm-schema binary if installed, otherwise the `helm schema` plugin.
set -eu
if command -v helm-schema >/dev/null 2>&1; then
  (cd charts/larakube && helm-schema -k additionalProperties)
else
  (cd charts/larakube && helm schema -k additionalProperties)
fi
(cd charts/larakube && helm-docs)
