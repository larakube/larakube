---
title: Docker image and registry
description: Docker image and registry
---


```bash
kubectl create secret docker-registry docker-registry-credentials -n "my-namespace" --docker-server="--" --docker-username="--" --docker-password="--"
```

```bash
kubectl modify-secret docker-registry-credentials -n "my-namespace"
```


You can use the `docker-registry-credentials` secret to authenticate with a private Docker registry.

You can then use the secret in the `values.yml` file to pull the Docker image from the private registry:

```yaml
imagePullSecrets:
  - name: docker-registry-credentials
```
