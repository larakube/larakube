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


## Run commands after a deployment

LaraKube can run commands once after every `helm install` and `helm upgrade`, for example to clear the application
cache. The commands run in a separate Job that uses the same image, environment and secrets as the application.

```yaml
postDeployment:
  enabled: true
```

By default the Job runs `php artisan cache:clear`. You can replace the command:

```yaml
postDeployment:
  enabled: true
  command:
    - /bin/sh
    - -c
    - php artisan cache:clear && php artisan queue:restart
```

If [database migrations](#database-migrations) are enabled, the post-deployment Job starts after the migration Job
finished successfully. If the command fails, `helm upgrade` reports the deployment as failed. Use
`postDeployment.retry` to retry it.

> [!NOTE]
> The Job runs in its own pod. Commands that only write files inside the container, like `config:cache`,
> `route:cache` or `optimize`, do not affect the web and worker pods. Run those when building the image instead.
> Use the post-deployment Job for shared state like the cache store, the database or the queue.

Helm runs the Job once the new resources are applied, which can be before every new pod is ready.
Add `--wait` to `helm upgrade` if the command must run after the rollout has finished.

## Database migrations

Enable `databaseMigration` to run `php artisan migrate --isolated --force` in a Job after every install and upgrade:

```yaml
databaseMigration:
  enabled: true
```
