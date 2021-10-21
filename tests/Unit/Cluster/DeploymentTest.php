<?php

use Larakube\Cluster\Deployment;
use Larakube\Cluster\EnvironmentVariable;

test('default setters and getters ', function () {
    $deployment = Deployment::create('laravel');

    expect($deployment->getName())->toBe('laravel');
    expect($deployment->getDockerFilePath())->toBe('');
    expect($deployment->getNamespace())->toBe('default');
    expect($deployment->getReplicaCount())->toBe(1);
    expect($deployment->getContainerPort())->toBeNull();
    expect($deployment->getContainerImage())->toBe('');
});

test('setters and getters ', function () {
    $deployment = Deployment::create('mysql')
        ->setDockerFilePath('Dockerfile')
        ->setNamespace('production')
        ->setReplicaCount(2)
        ->setContainerPort(80)
        ->setContainerImage('mysql');

    expect($deployment->getName())->toBe('mysql');
    expect($deployment->getDockerFilePath())->toBe('Dockerfile');
    expect($deployment->getNamespace())->toBe('production');
    expect($deployment->getReplicaCount())->toBe(2);
    expect($deployment->getContainerPort())->toBe(80);
    expect($deployment->getContainerImage())->toBe('mysql:latest');
});

it('can generate a valid kubernetes deployment', function () {
    $deployment = Deployment::create('laravel')
        ->setContainerPort(80);

    $expected = "apiVersion: apps/v1
kind: Deployment
metadata:
  name: laravel
  namespace: default
spec:
  replicas: 1
  selector:
    matchLabels:
      app: laravel
  template:
    apiVersion: v1
    kind: Pod
    metadata:
      name: laravel
      namespace: default
      labels:
        app: laravel
    spec:
      containers:
        -
          name: laravel
          image: laravel
          imagePullPolicy: IfNotPresent
          ports:
            -
              name: http
              protocol: TCP
              containerPort: 80
";

    expect($deployment->toManifest())->toBe($expected);
});

it('can generate a valid kubernetes deployment with replicas', function () {
    $deployment = Deployment::create('laravel')
        ->setDockerFilePath('Dockerfile')
        ->setReplicaCount(2)
        ->setContainerPort(80);

    $expected = "apiVersion: apps/v1
kind: Deployment
metadata:
  name: laravel
  namespace: default
spec:
  replicas: 2
  selector:
    matchLabels:
      app: laravel
  template:
    apiVersion: v1
    kind: Pod
    metadata:
      name: laravel
      namespace: default
      labels:
        app: laravel
    spec:
      containers:
        -
          name: laravel
          image: laravel
          imagePullPolicy: IfNotPresent
          ports:
            -
              name: http
              protocol: TCP
              containerPort: 80
";

    expect($deployment->toManifest())->toBe($expected);
});

it('can generate a valid kubernetes deployment without ports', function () {
    $deployment = Deployment::create('laravel');

    $expected = "apiVersion: apps/v1
kind: Deployment
metadata:
  name: laravel
  namespace: default
spec:
  replicas: 1
  selector:
    matchLabels:
      app: laravel
  template:
    apiVersion: v1
    kind: Pod
    metadata:
      name: laravel
      namespace: default
      labels:
        app: laravel
    spec:
      containers:
        -
          name: laravel
          image: laravel
          imagePullPolicy: IfNotPresent
";

    expect($deployment->toManifest())->toBe($expected);
});

it('can generate a valid kubernetes deployment with manually setting image', function () {
    $deployment = Deployment::create('mysql')
        ->setContainerImage('mysql');

    $expected = "apiVersion: apps/v1
kind: Deployment
metadata:
  name: mysql
  namespace: default
spec:
  replicas: 1
  selector:
    matchLabels:
      app: mysql
  template:
    apiVersion: v1
    kind: Pod
    metadata:
      name: mysql
      namespace: default
      labels:
        app: mysql
    spec:
      containers:
        -
          name: mysql
          image: 'mysql:latest'
          imagePullPolicy: IfNotPresent
";

    expect($deployment->toManifest())->toBe($expected);
});

it('can generate a valid kubernetes deployment with secrets', function () {
    $deployment = Deployment::create('mysql')
        ->setContainerImage('mysql')
        ->setEnvironmentVariable(new EnvironmentVariable('MYSQL_ROOT_PASSWORD'));

    $expected = "apiVersion: apps/v1
kind: Deployment
metadata:
  name: mysql
  namespace: default
spec:
  replicas: 1
  selector:
    matchLabels:
      app: mysql
  template:
    apiVersion: v1
    kind: Pod
    metadata:
      name: mysql
      namespace: default
      labels:
        app: mysql
    spec:
      containers:
        -
          name: mysql
          image: 'mysql:latest'
          imagePullPolicy: IfNotPresent
          env:
            -
              name: MYSQL_ROOT_PASSWORD
              valueFrom:
                secretKeyRef:
                  name: mysql
                  key: mysql_root_password
";

    expect($deployment->toManifest())->toBe($expected);
});
