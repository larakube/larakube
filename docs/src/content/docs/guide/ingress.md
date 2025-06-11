---
title: Ingress
description: How to get web traffic to your Laravel application.
---

## Using Traefik

Traefik is a modern, open-source edge router and reverse proxy that enables users to manage and configure incoming traffic to their applications, making it a popular choice for Kubernetes deployments.
In a Kubernetes environment, Traefik can be used as an ingress controller, allowing users to define routing rules and manage access to their services using a simple and intuitive configuration file.
By leveraging Traefik's integration with Kubernetes, users can easily expose their services to the outside world, handle SSL termination, and implement advanced routing and authentication mechanisms.

To use Traefik as an ingress controller in LaraKube, you can enable it in the `values.yml` file:

```yaml
web:
  traefik:
    enabled: true
    domain: "my-application-domain.com" # The domain to use for Traefik
```

### TLS with Traefik

TODO

### Custom request and response headers

TODO

### Restrict access with basic auth

You can restrict access to your application using basic authentication by configuring the `web.traefik.basicAuth` settings in the `values.yml` file.
This can be useful to protect staging environments or applications that are not yet ready for public access.

Before you can use basic authentication, you need to create a Kubernetes secret that contains the user credentials. You can do this using the following command:

> [!NOTE]  
> Do not enter the password in the command line, as it will be visible in the command history. The next command will open an editor where you can enter the password.

```bash
kubectl create secret generic my-application-basic-auth-secret --namespace=my-application-namespace --from-literal=username= --from-literal=password= --type=kubernetes.io/basic-auth
```

After creating the empty secret, you can modify it to add the password with [kubectl-modify-secret](getting-started.md#kubectl-modify-secret):

```bash
kubectl modify-secret my-application-basic-auth-secret -n my-application-namespace
```

You can then enable basic authentication in the `values.yml` file like this:

```yaml
web:
  traefik:
    basicAuth:
      enabled: true
      realm: "My application"
      secret: "my-application-basic-auth-secret" # Name of the secret that contains the user credentials
```

This will enable basic authentication for your application, requiring users to provide a username and password to access it.
The `realm` is the name of the site that you are restricting access to, and it will be displayed in the authentication prompt.

#### Bypass basic authentication for specific IP ranges

You can bypass basic authentication for specific IP addresses or IP ranges by adding the `web.traefik.allowBypassForIpRanges` configuration in the `values.yml` file.

```yaml
web:
  traefik:
    enabled: true
    realm: "My application"
    secret: "my-application-basic-auth-secret"
    allowBypassForIpRanges:
      - "x.x.x.x"
      - "y.y.y.y/24"
```
