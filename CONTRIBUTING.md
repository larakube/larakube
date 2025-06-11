# Contributing

## Before you commit

If possible, please run the following commands before submit a PR.

```bash
helm schema -k additionalProperties
```

```bash
helm-docs
```

This command creates a Markdown table in `charts/larakube`. Check and then copy the table to the main `readme.md` and to `docs/src/content/reference/chart-config.yml`.

