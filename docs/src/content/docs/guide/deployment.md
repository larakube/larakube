---
title: Deployment
description: Deploying an application with LaraKube
---

```bash
helm upgrade --install \
    --namespace=your-namespace \
    fastfront -f values.yml larakube/larakube
```

```bash
kubectl rollout restart deployment --namespace your-namespace you-app-web you-app-worker-default
```

