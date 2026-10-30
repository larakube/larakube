#!/usr/bin/env sh
# Render the chart with every CI values file and validate the manifests with kubeconform.
# Traefik and cert-manager resources are checked against the CRD schemas from datreeio/CRDs-catalog.
set -eu

CHART_DIR="charts/larakube"
KUBERNETES_VERSION="${KUBERNETES_VERSION:-1.31.0}"
# Pinned commit of https://github.com/datreeio/CRDs-catalog
CRDS_CATALOG_REF="63669a570e231d4f1f8396d229a1de512bcf0a34"

for values in "$CHART_DIR"/ci/*-values.yaml; do
  echo "==> $values"
  helm template ci "$CHART_DIR" --values "$values" | kubeconform \
    -strict \
    -summary \
    -kubernetes-version "$KUBERNETES_VERSION" \
    -schema-location default \
    -schema-location "https://raw.githubusercontent.com/datreeio/CRDs-catalog/$CRDS_CATALOG_REF/{{.Group}}/{{.ResourceKind}}_{{.ResourceAPIVersion}}.json"
done
