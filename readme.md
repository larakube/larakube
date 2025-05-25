![ArtifactHUB](https://img.shields.io/endpoint?url=https%3A%2F%2Fartifacthub.io%2Fbadge%2Frepository%2Flarakube&style=flat-square)
![GitHub License](https://img.shields.io/github/license/larakube/larakube?style=flat-square)
![GitHub Release](https://img.shields.io/github/v/release/larakube/larakube?style=flat-square)
![GitHub Actions Workflow Status - Lint](https://img.shields.io/github/actions/workflow/status/larakube/larakube/lint.yml?style=flat-square&label=Lint)

# LaraKube

[LaraKube](http://www.larakube.com/) is a Helm chart to run Laravel applications on Kubernetes (K8S).

<br><br>
<a href="https://www.larakube.com/"><img alt="Docs Badge" src="https://img.shields.io/badge/Docs-yellow?style=flat-square&logo=data:image/svg%2bxml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9Im5vbmUiIHZpZXdCb3g9IjAgMCAyNCAyNCIgc3Ryb2tlLXdpZHRoPSIxLjUiIHN0cm9rZT0id2hpdGUiIGNsYXNzPSJzaXplLTYiPgogIDxwYXRoIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgZD0iTTEyIDYuMDQyQTguOTY3IDguOTY3IDAgMCAwIDYgMy43NWMtMS4wNTIgMC0yLjA2Mi4xOC0zIC41MTJ2MTQuMjVBOC45ODcgOC45ODcgMCAwIDEgNiAxOGMyLjMwNSAwIDQuNDA4Ljg2NyA2IDIuMjkybTAtMTQuMjVhOC45NjYgOC45NjYgMCAwIDEgNi0yLjI5MmMxLjA1MiAwIDIuMDYyLjE4IDMgLjUxMnYxNC4yNUE4Ljk4NyA4Ljk4NyAwIDAgMCAxOCAxOGE4Ljk2NyA4Ljk2NyAwIDAgMC02IDIuMjkybTAtMTQuMjV2MTQuMjUiIC8+Cjwvc3ZnPg==" height="50"></a>
<a href="https://artifacthub.io/packages/helm/larakube/larakube"><img alt="ArtifactHUB Badge" src="https://img.shields.io/badge/ArtifactHUB-417598?style=flat-square&&logo=artifacthub&logoColor=white" height="50"></a>

## Features

- [x] Horizontal autoscaling
- [x] Multiple queue workers
- [x] K8S-native Laravel scheduler
- [X] Ingress configuration for Traefik
- [X] Useful middlewares for Traefik
  - [X] Redirect to HTTPS
  - [X] Compression
- [X] Certificates management with Cert-Manager

## Values

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| affinity | object | `{}` | Affinity rules for scheduling the pod. |
| databaseMigration.enabled | bool | `false` | Enable or disable database migrations. |
| databaseMigration.env | object | `{}` | Environment variables specific to the database migration container. |
| databaseMigration.resources | object | `{}` | Resource requests and limits for the database migration container. |
| databaseMigration.retry | int | `0` | The number of times to retry the database migration. |
| env | object | `{}` | Environment variables to set in the container. |
| fullnameOverride | string | `""` | Overrides the full name of the chart. |
| image.pullPolicy | string | `"IfNotPresent"` | The policy for pulling the Docker image (e.g., IfNotPresent, Always). |
| image.repository | string | `""` |  |
| image.tag | string | `""` | Overrides the image tag whose default is the chart appVersion. |
| imagePullSecrets | list | `[]` | List of secrets to use for pulling the Docker image. |
| ingress.annotations | object | `{}` | Annotations to add to the ingress resource. |
| ingress.className | string | `""` | The ingress class name. |
| ingress.enabled | bool | `false` | Enable or disable the ingress resource. |
| ingress.hosts | list | `[{"host":"chart-example.local","paths":[{"path":"/","pathType":"ImplementationSpecific"}]}]` | The hostname to use for the ingress. |
| ingress.hosts[0].paths | list | `[{"path":"/","pathType":"ImplementationSpecific"}]` | The path to use for the ingress. |
| ingress.hosts[0].paths[0].pathType | string | `"ImplementationSpecific"` | The type of path (e.g., ImplementationSpecific, Exact, Prefix). |
| ingress.tls | list | `[]` | TLS configuration for the ingress. |
| nameOverride | string | `""` | Overrides the name of the chart. |
| nodeSelector | object | `{}` | Node selector for scheduling the pod. |
| podAnnotations | object | `{}` | Annotations to add to the pod. |
| podLabels | object | `{}` | Labels to add to the pod. |
| podSecurityContext | object | `{}` | Security context for the pod. |
| revisionHistoryLimit | int | `2` | The number of old ReplicaSets to retain. |
| scheduler.command | list | `["/bin/sh","-c","php artisan schedule:run"]` | The command to run the scheduler. |
| scheduler.enabled | bool | `true` | Enable or disable the scheduler. |
| scheduler.env | object | `{}` | Environment variables specific to the scheduler container. |
| scheduler.resources | object | `{}` | Resource requests and limits for the scheduler container. |
| scheduler.schedule | string | `"* * * * *"` | The schedule for the scheduler (e.g., every minute). |
| secretEnvs | list | `[]` | Secret environment variables to set in the container. |
| securityContext | object | `{}` | Security context for the container. |
| service.containerPort | int | `8000` | The port on which the container will listen. |
| service.port | int | `80` | The port on which the service will be exposed. |
| service.type | string | `"ClusterIP"` | The type of service (e.g., ClusterIP, NodePort, LoadBalancer). |
| serviceAccount.annotations | object | `{}` | Annotations to add to the service account. |
| serviceAccount.automount | bool | `true` | Automatically mount a ServiceAccount's API credentials? |
| serviceAccount.create | bool | `true` | Specifies whether a service account should be created. |
| serviceAccount.name | string | `""` | The name of the service account to use. If not set and create is true, a name is generated using the fullname template. |
| tolerations | list | `[]` | Tolerations for scheduling the pod. |
| topologySpreadConstraints | list | `[]` | Topology spread constraints for scheduling the pod. |
| volumeMounts | list | `[]` | Additional volumeMounts on the output Deployment definition. |
| volumes | list | `[]` | Additional volumes on the output Deployment definition. |
| web.affinity | object | `{}` | Affinity rules for the web deployment. |
| web.autoscaling | object | `{"enabled":false,"maxReplicas":100,"minReplicas":1,"targetCPUUtilizationPercentage":80}` | Autoscaling configuration for the web deployment. |
| web.autoscaling.enabled | bool | `false` | Enable or disable autoscaling for the web deployment. |
| web.autoscaling.maxReplicas | int | `100` | The maximum number of replicas for autoscaling. |
| web.autoscaling.minReplicas | int | `1` | The minimum number of replicas for autoscaling. |
| web.autoscaling.targetCPUUtilizationPercentage | int | `80` | The target CPU utilization percentage for autoscaling. |
| web.certManager | object | `{"domains":[],"enabled":false,"issuer":""}` | Cert-Manager configuration for managing TLS certificates. |
| web.certManager.domains | list | `[]` | The domains to use for Cert-Manager. |
| web.certManager.enabled | bool | `false` | Enable or disable Cert-Manager. |
| web.certManager.issuer | string | `""` | The issuer to use for Cert-Manager. |
| web.env | object | `{}` | Environment variables specific to the web container. |
| web.livenessProbe | object | `{}` | Liveness probe configuration for the web container. |
| web.pdb | object | `{"enabled":false,"maxUnavailable":0}` | PodDisruptionBudget configuration for the web deployment. |
| web.pdb.enabled | bool | `false` | Enable or disable the PodDisruptionBudget for the web deployment. |
| web.pdb.maxUnavailable | int | `0` | The maximum number of pods that can be unavailable during a disruption. minAvailable: 1 |
| web.readinessProbe | object | `{}` | Readiness probe configuration for the web container. |
| web.replicaCount | int | `1` | The number of replicas for the web deployment. |
| web.resources | object | `{}` | Resource requests and limits for the web container. |
| web.startupProbe | object | `{}` | Startup probe configuration for the web container. |
| web.tolerations | list | `[]` | Tolerations for the web deployment. |
| web.topologySpreadConstraints | list | `[]` | Topology spread constraints for the web deployment. |
| web.traefik.basicAuth | object | `{"enabled":false,"realm":"","secret":""}` | Configuration for basic authentication |
| web.traefik.basicAuth.enabled | bool | `false` | Enable or disable basic authentication for Traefik. |
| web.traefik.basicAuth.realm | string | `""` | Basic auth realm (f.e. name of the site that you are restricting access to) |
| web.traefik.basicAuth.secret | string | `""` | Name of the secret that contains the user credentials. See https://doc.traefik.io/traefik/middlewares/http/basicauth/#users for more info |
| web.traefik.compress | bool | `true` | Enable or disable compression for Traefik. |
| web.traefik.customRequestHeaders | object | `{}` | Custom request headers to use for Traefik. |
| web.traefik.customResponseHeaders | object | `{}` | Custom response headers to use for Traefik. |
| web.traefik.domain | string | `""` | The domain to use for Traefik. |
| web.traefik.domainRedirects | list | `[]` | Domains that redirect to the main domain (f.e. redirect www to non-www) Please keep in mind that the certificate need include this domain as well. Example:  - domain: www.some-site.test |
| web.traefik.enabled | bool | `false` | Enable or disable Traefik ingress. |
| web.traefik.extraMiddlewares | list | `[]` | Extra middlewares to use for Traefik. |
| web.updateStrategy | object | `{"rollingUpdate":{"maxSurge":1,"maxUnavailable":0},"type":"RollingUpdate"}` | Update strategy for the web deployment. |
| web.updateStrategy.rollingUpdate | object | `{"maxSurge":1,"maxUnavailable":0}` | Rolling update configuration. |
| web.updateStrategy.rollingUpdate.maxSurge | int | `1` | The maximum number of pods that can be created above the desired number of pods during an update. |
| web.updateStrategy.rollingUpdate.maxUnavailable | int | `0` | The maximum number of pods that can be unavailable during an update. |
| web.updateStrategy.type | string | `"RollingUpdate"` | The update strategy for the web deployment (e.g., RollingUpdate, Recreate). |
| worker.default.autoscaling | object | `{"enabled":false,"maxReplicas":100,"minReplicas":1,"targetCPUUtilizationPercentage":80}` | Autoscaling configuration for the worker deployment. |
| worker.default.autoscaling.enabled | bool | `false` | Enable or disable autoscaling for the worker deployment. |
| worker.default.autoscaling.maxReplicas | int | `100` | The maximum number of replicas for autoscaling. |
| worker.default.autoscaling.minReplicas | int | `1` | The minimum number of replicas for autoscaling. |
| worker.default.autoscaling.targetCPUUtilizationPercentage | int | `80` | The target CPU utilization percentage for autoscaling. |
| worker.default.env | object | `{}` | Environment variables specific to the worker container. |
| worker.default.livenessProbe | object | `{}` | Liveness probe configuration for the worker container. |
| worker.default.pdb.enabled | bool | `false` | Enable or disable the PodDisruptionBudget for the worker deployment. |
| worker.default.pdb.maxUnavailable | int | `0` | The maximum number of pods that can be unavailable during a disruption. minAvailable: 1 |
| worker.default.readinessProbe | object | `{}` | Readiness probe configuration for the worker container. |
| worker.default.replicaCount | int | `1` | The number of replicas for the worker deployment. |
| worker.default.resources | object | `{}` | Resource requests and limits for the worker container. |
| worker.default.startupProbe | object | `{}` | Startup probe configuration for the worker container. |
| worker.default.updateStrategy.type | string | `"Recreate"` | The update strategy for the worker deployment (e.g., RollingUpdate, Recreate). |


## Contributing

Contributions are welcome.

## License

This project is licensed under the Apache License 2.0. Please see [license file](license.md) for more information.

## Credits

Thanks to [mrbenosborne](https://github.com/mrbenosborne) for donating the "larakube" GitHub organization name.
