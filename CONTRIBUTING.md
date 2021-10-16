# Contributing
Hey there! You have landed here which means you may be interested in contributing, that's great news, and we welcome you to help with new features and maintenance of Larakube.

## Testing Environment
Because this package is quite complex the tests are a little complex too, for example building docker images, pushing images to a repository and deploying to a Kubernetes cluster. There are a few dependencies you will need to download and install before you can run the feature tests.

1. [Requirements](#Requirements)
    1. [Installing Minikube](#minikube) 
    2. [Installing Docker](#docker) 
    3. [PHP](#php) 
2. [Prerequisites](#prerequisites)
    1. [Starting Minikube](#start-minikube)
    2. [Enable Container Registry](#enable-minikube-container-registry)
4. [Run Tests](#run-tests)

### Requirements
This section lists out all the required dependencies you will need.

#### Minikube
Version: v1.23

[Download & Install v1.22](https://minikube.sigs.k8s.io/docs/start/)

#### Docker
Version: 20.*

[Download & Install](https://docs.docker.com/get-docker/)

#### PHP
Version: ^7.4 and ^8.0

### Prerequisites
Before you run any tests, mainly feature tests you will need to perform the following actions.

#### Start Minikube
Start a local Kubernetes cluster using the command below:

```
minikube start
```

#### Enable Minikube Container Registry
Enable the registry addon for Minikube.

```
minikube addons enable registry
```

### Run Tests
We use PestPHP for our tests, run the following command to run our test suites.

```
./vendor/bin/pest
```