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

The chart creates an `IngressRoute` on the `websecure` entry point for `web.traefik.domain` and a second one on the
`web` entry point that permanently redirects HTTP to HTTPS. Responses are compressed by default; set
`web.traefik.compress: false` to turn that off.

### Traefik v2

The chart targets Traefik v3 and creates its resources with the `traefik.io/v1alpha1` CRDs. If your cluster still
runs Traefik v2, set `web.traefik.version` to `2` to use the `traefik.containo.us/v1alpha1` CRDs instead:

```yaml
web:
  traefik:
    enabled: true
    version: 2
```

> [!NOTE]
> Before chart version 0.3.0, the chart always used the Traefik v2 CRDs (see [Upgrading](/guide/upgrading/#upgrading-to-030)). When you upgrade, either set
> `web.traefik.version: 2` or make sure your Traefik serves `traefik.io/v1alpha1` (Traefik v3, or v2.10 and later).
> Switching the CRD group replaces the Traefik resources, so expect a few seconds without routes during that deploy.

### TLS with Traefik

The IngressRoutes terminate TLS with the secret `<release-fullname>-cert-secret`. The easiest way to fill it is to let
[cert-manager](https://cert-manager.io/) issue the certificate:

```yaml
web:
  certManager:
    enabled: true
    issuer: letsencrypt-production # Name of your issuer
    issuerKind: ClusterIssuer # Or "Issuer" for a namespaced issuer
    domains:
      - my-application-domain.com
      - www.my-application-domain.com # Include every domain from web.traefik.domainRedirects
  traefik:
    enabled: true
    domain: my-application-domain.com
```

If you manage the certificate yourself (for example a wildcard certificate), point the routes at your secret instead
and leave cert-manager disabled:

```yaml
web:
  traefik:
    enabled: true
    domain: my-application-domain.com
    tlsSecretName: my-wildcard-certificate
```

### Redirect other domains

Use `web.traefik.domainRedirects` to permanently redirect other domains (for example `www`) to `web.traefik.domain`.
The path is kept. Make sure the TLS certificate covers these domains as well.

```yaml
web:
  traefik:
    domain: my-application-domain.com
    domainRedirects:
      - domain: www.my-application-domain.com
```

### Custom request and response headers

Add headers to every request sent to your application or every response sent to the client:

```yaml
web:
  traefik:
    customRequestHeaders:
      X-Forwarded-Proto: https
    customResponseHeaders:
      X-Frame-Options: SAMEORIGIN
      Strict-Transport-Security: max-age=31536000; includeSubDomains
```

An empty string removes a header. To use other Traefik middlewares, create them yourself and reference them by name
with `web.traefik.extraMiddlewares`.

### Restrict access with basic auth

You can restrict access to your application using basic authentication by configuring the `web.traefik.basicAuth` settings in the `values.yml` file.
This can be useful to protect staging environments or applications that are not yet ready for public access.

Before you can use basic authentication, you need to create a Kubernetes secret that contains the user credentials. You can do this using the following command:

> [!NOTE]  
> Do not enter the password in the command line, as it will be visible in the command history. The next command will open an editor where you can enter the password.

```bash
kubectl create secret generic my-application-basic-auth-secret --namespace=my-application-namespace --from-literal=username= --from-literal=password= --type=kubernetes.io/basic-auth
```

After creating the empty secret, you can modify it to add the password with [kubectl-modify-secret](../getting-started#kubectl-modify-secret):

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

You can bypass basic authentication for specific IP addresses or IP ranges with `web.traefik.basicAuth.allowBypassForIpRanges`:

```yaml
web:
  traefik:
    enabled: true
    basicAuth:
      enabled: true
      realm: "My application"
      secret: "my-application-basic-auth-secret"
      allowBypassForIpRanges:
        - "203.0.113.10"
        - "198.51.100.0/24"
```

> [!CAUTION]
> Traefik matches the IP of the connection it receives. Behind a cloud load balancer without
> `externalTrafficPolicy: Local` or the PROXY protocol, or behind a CDN, every request comes from the same
> load balancer or proxy IP. Depending on your ranges, that means nobody or **everybody** bypasses basic auth.
> Check which client IP Traefik sees (for example in its access log) before relying on this.
