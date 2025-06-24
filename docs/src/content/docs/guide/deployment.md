---
title: Deployment
description: Deploying an application with LaraKube
---

```bash
helm upgrade --install \
    --namespace=your-namespace \
    your-app -f values.yml larakube/larakube
```

The following command can be used to force a redeployment of the application.

```bash
kubectl rollout restart deployment --namespace your-namespace your-app-web your-app-worker-default
```

This can be useful for example in one of the following cases:
 - You changed the value of a secret and want to apply it to the application
 - The image of the application was updated, but the tag of the image did not change (f.e. the tag is `latest` or `develop`).

