---
title: Upgrading
description: How to upgrade LaraKube and what changes between chart versions.
---

## How to upgrade

Update the chart repository and upgrade your release like any other deployment:

```bash
helm repo update larakube
helm upgrade --install \
    --namespace=your-namespace \
    your-app -f values.yml larakube/larakube --version 0.3.0
```

Before upgrading a production release, preview what will change. The [helm-diff](https://github.com/databus23/helm-diff)
plugin shows the difference between the running release and the new version:

```bash
helm diff upgrade \
    --namespace=your-namespace \
    your-app -f values.yml larakube/larakube --version 0.3.0
```

Every chart upgrade restarts all pods, because the chart version is part of the pod labels.

## Upgrading to 0.3.0

0.3.0 switches to Traefik v3 by default and fixes several settings that were ignored before.
Go through the following list before you upgrade. Most installations only need the first item.

### Traefik v2 users must set `web.traefik.version`

The chart now creates its Traefik resources with the Traefik v3 CRDs (`traefik.io/v1alpha1`).
Earlier versions used the Traefik v2 CRDs (`traefik.containo.us/v1alpha1`).

- **Traefik v2.9 or older:** the upgrade fails because the cluster does not know the new CRDs. Keep the old CRDs:

  ```yaml
  web:
    traefik:
      version: 2
  ```

- **Traefik v2.10 or newer:** both CRD groups exist, so the default works. Helm creates the new resources and removes
  the old ones, which can cause a short interruption of the routes. Set `version: 2` if you want to switch later.

### `maxUnavailable: 0` in a PodDisruptionBudget is now applied

Before 0.3.0, a value of `0` for `web.pdb.maxUnavailable` or `worker.<name>.pdb.maxUnavailable` was silently dropped.
It is now applied as written. A PodDisruptionBudget with `maxUnavailable: 0` blocks every voluntary eviction, so
`kubectl drain`, node upgrades and the cluster autoscaler can hang.

If you set `maxUnavailable: 0`, change it, for example:

```yaml
web:
  replicaCount: 2
  pdb:
    enabled: true
    maxUnavailable: 1
```

If `pdb.enabled` is `true`, you must now set exactly one of `minAvailable` or `maxUnavailable`. Otherwise the chart
fails to render with an error message.

### The scheduler uses `scheduler.env`

The scheduler CronJob used to receive `web.env` instead of `scheduler.env`. It now uses `scheduler.env`.
If your scheduled tasks rely on a variable from `web.env`, move it to the global `env` or add it to `scheduler.env`.

### Workers use their update strategy

`worker.<name>.updateStrategy` was ignored, so workers always used a rolling update. Now the configured strategy is
used, and the default is `Recreate`: all pods of a worker stop before the new ones start. Queued jobs wait during that
time. A job that is still running when its pod is killed is interrupted. Laravel releases it again after the queue's
`retry_after`, as long as the job has tries left.

To keep the previous behaviour:

```yaml
worker:
  default:
    updateStrategy:
      type: RollingUpdate
```

### Worker probes and pod settings are applied

The following settings were documented but not applied to worker pods. Check them if you had set them:

- `worker.<name>.livenessProbe`, `readinessProbe` and `startupProbe`. A wrong liveness probe now restarts your workers.
- The global `podAnnotations`, `podLabels`, `volumes`, `volumeMounts`, `nodeSelector`, `affinity`, `tolerations` and
  `topologySpreadConstraints`. Setting most of these used to break the deployment, so if you could deploy before,
  you were not using them.

### `web.affinity` is applied

`web.affinity` was ignored, and the web pods always used the global `affinity`. Now `web.affinity` is used when it is
set and the global `affinity` otherwise, like `web.tolerations` and `web.topologySpreadConstraints`. If you had set
`web.affinity`, your web pods are rescheduled according to it.

### The migration Job uses the global scheduling settings

The database migration Job now uses the global `nodeSelector`, `affinity` and `tolerations`, like the other pods.
Before, it ignored them and could stay `Pending` on clusters with tainted nodes.

### Every `secretEnvs` entry is applied

Before 0.3.0, only `secretEnvs` entries with `existing: true` were applied; all others were ignored without an error.
Now every entry is applied, and each one needs `name`, `secretName` and `secretKey`, otherwise the chart fails with an
error message. `existing` is no longer needed. Check that every entry points at a Secret and key that exist, otherwise
the pods fail to start.

### Other changes

- The standard `ingress` now routes to the `<release>-web` Service. Before, it pointed at a Service that does not exist.
- The chart fails with an error message if `web.traefik.basicAuth` is enabled without a `secret`, or
  `web.certManager` is enabled without an `issuer`. These setups did not work before either.
- The chart fails with an error message if a worker enables `autoscaling` without `maxReplicas`, or a
  `web.traefik.domainRedirects` entry uses a wildcard domain. These setups did not work before either.
  `worker.<name>.autoscaling.minReplicas` now defaults to `1`.
- If `web.traefik` is enabled without `web.certManager` or `web.traefik.tlsSecretName`, the install notes warn that
  the TLS secret is missing.
- New, optional settings: `web.traefik.tlsSecretName`, `web.certManager.issuerKind`,
  `worker.<name>.command` / `args` ([queue workers](/guide/deployment/#queue-workers)) and
  [`postDeployment`](/guide/deployment/#run-commands-after-a-deployment).
