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
