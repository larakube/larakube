---
title: Helm Chart config reference
description: A reference of the available config variables of the Helm chart
---

This reference explains the config values of the Helm chart.


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
| web.preStopCommand | list | `[]` | Command that runs before the container is terminated. |
| web.readinessProbe | object | `{}` | Readiness probe configuration for the web container. |
| web.replicaCount | int | `1` | The number of replicas for the web deployment. |
| web.resources | object | `{}` | Resource requests and limits for the web container. |
| web.startupProbe | object | `{}` | Startup probe configuration for the web container. |
| web.terminationGracePeriodSeconds | int | `30` | The termination grace period in seconds for the web deployment. |
| web.tolerations | list | `[]` | Tolerations for the web deployment. |
| web.topologySpreadConstraints | list | `[]` | Topology spread constraints for the web deployment. |
| web.traefik.basicAuth | object | `{"allowBypassForIpRanges":[],"enabled":false,"realm":"","secret":""}` | Configuration for basic authentication |
| web.traefik.basicAuth.allowBypassForIpRanges | list | `[]` | List of IP ranges that are allowed to bypass the basic auth. |
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
| worker.default.pdb.maxUnavailable | string | `nil` | The maximum number of pods that can be unavailable during a disruption. @schema anyOf:   - type: "null"   - type: string   - type: integer @schema |
| worker.default.pdb.minAvailable | string | `nil` | The minimum number of pods that must be available during a disruption. @schema anyOf:   - type: "null"   - type: string   - type: integer @schema |
| worker.default.readinessProbe | object | `{}` | Readiness probe configuration for the worker container. |
| worker.default.replicaCount | int | `1` | The number of replicas for the worker deployment. |
| worker.default.resources | object | `{}` | Resource requests and limits for the worker container. |
| worker.default.startupProbe | object | `{}` | Startup probe configuration for the worker container. |
| worker.default.updateStrategy.type | string | `"Recreate"` | The update strategy for the worker deployment (e.g., RollingUpdate, Recreate). |
