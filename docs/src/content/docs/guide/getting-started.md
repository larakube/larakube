---
title: Getting started
description: Installing the LaraKube helm chart
---

LaraKube is a Helm chart to run Laravel applications on Kubernetes (K8S).
To use the chart you first need to install Helm and kubectl and configure both to manage the K8S cluster of your choice.

After the preparations you can install the Helm chart with the following command.

```bash
helm repo add larakube https://charts.larakube.com
```

You can update the repository with the following command:
```bash
helm repo update larakube
```

To get started with LaraKub you need a config file named `values.yml`.
You can copy the [default configuration file from the LaraKube repository](https://raw.githubusercontent.com/larakube/larakube/refs/heads/main/charts/larakube/values.yaml).

If you want you can create a new namespace for your application, for example `my-application-staging`:

```bash
kubectl create namespace my-application-staging
```

## About the guide

The next chapters to this guide will explain how to configure the `values.yml` file for your application.
After that the chapter [Deployment](../deployment) will explain how to then deploy your application with LaraKube.

## Tools & Extensions

To make working with LaraKube easier, you can use some tools and extensions.

### kubectl-modify-secret

The guides in LaraKube use the [kubectl-modify-secret](https://github.com/rajatjindal/kubectl-modify-secret) extension for `kubectl` to modify secrets.
