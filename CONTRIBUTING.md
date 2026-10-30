# Contributing

## Before you commit

If possible, please run the following commands before submit a PR.

```bash
helm schema -k additionalProperties
```

```bash
helm-docs
```

This command creates a Markdown table in `charts/larakube`. Check and then copy the table to the main `readme.md` and to `docs/src/content/docs/reference/chart-config.md`.

## Tests

CI runs the following checks on every push and pull request. You can run them locally:

```bash
# Unit tests (charts/larakube/unittests/*_test.yaml) with helm-unittest
helm unittest -f 'unittests/*_test.yaml' charts/larakube
# or without installing the plugin:
docker run --rm -v "$PWD/charts/larakube:/apps" helmunittest/helm-unittest:4.3.0-1.2.1 -f 'unittests/*_test.yaml' .

# Validate the manifests rendered from charts/larakube/ci/*-values.yaml (needs kubeconform)
./scripts/kubeconform.sh

# Check that values.schema.json and the values tables match values.yaml
./scripts/check-generated.sh
```

When you fix a bug or add a feature, add a test case to the matching file in `charts/larakube/unittests/`.
If you add an optional feature, enable it in `charts/larakube/ci/full-values.yaml` as well.

