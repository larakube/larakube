![ArtifactHUB](https://img.shields.io/endpoint?url=https%3A%2F%2Fartifacthub.io%2Fbadge%2Frepository%2Flarakube&style=flat-square)
![GitHub License](https://img.shields.io/github/license/solidtime-io/larakube?style=flat-square)
![GitHub Release](https://img.shields.io/github/v/release/solidtime-io/larakube?style=flat-square)
![GitHub Actions Workflow Status - Lint](https://img.shields.io/github/actions/workflow/status/solidtime-io/larakube/lint.yml?style=flat-square&label=Lint)

# LaraKube

[LaraKube](http://www.larakube.com/) is a Helm chart to run Laravel applications on Kubernetes (K8S).

Docs: [larakube.com](https://www.larakube.com/)  
ArtifactHUB: [artifacthub.io](https://artifacthub.io/packages/helm/larakube/larakube) 

> [!WARNING]
> This project is currently a work-in-progress and should be used with caution.

## Features

- [x] Horizontal autoscaling
- [x] Multiple queue workers
- [x] K8S-native Laravel scheduler
- [X] Ingress configuration for Traefik
- [X] Useful middlewares for Traefik
  - [X] Redirect to HTTPS
  - [X] Compression
- [X] Certificates management with Cert-Manager

## Contributing

Contributions are welcome.

## License

This project is licensed under the Apache License 2.0. Please see [license file](license.md) for more information.
