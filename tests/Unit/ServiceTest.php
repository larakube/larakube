<?php

use Larakube\Service;

beforeEach(fn() => Service::reset());

it('can get service by name', function () {
    Service::create('Laravel');

    $service = Service::get('Laravel');
    expect($service)->not()->toBeNull();
    expect($service->getName())->toBe('laravel');
});

it('can create a new service with a uppercase name', function () {
    $service = Service::create('Laravel');
    expect($service->getName())->toBe('laravel');
});

it('cannot create a duplicate service', function () {
    $service = Service::create('Laravel');
    expect($service)->not()->toBeNull();

    Service::create('Laravel');
})->expectExceptionMessage('service already exists');

it('does generate valid scaffold configuration', function () {
    config(['app.name' => 'Laravel', 'kube.services.path' => 'kube/services']);

    Service::create('Laravel')
        ->setDockerfile('Dockerfile');

    $expected = "apiVersion: skaffold/v2beta24
kind: Config
metadata:
  name: laravel
build:
  artifacts:
    -
      image: laravel
      context: .
      docker:
        dockerfile: kube/services/laravel/Dockerfile
deploy:
  kubectl:
    manifests:
      - 'kube/services/*'
    flags:
      disableValidation: true
";
    expect(Service::toYaml())->toBe($expected);
});

it('does generate valid scaffold configuration for two services', function () {
    config(['app.name' => 'Laravel', 'kube.services.path' => 'kube/services']);

    Service::create('Laravel')->setDockerfile('Dockerfile');
    Service::create('MySQL')->setDockerfile('Dockerfile');

    $expected = "apiVersion: skaffold/v2beta24
kind: Config
metadata:
  name: laravel
build:
  artifacts:
    -
      image: laravel
      context: .
      docker:
        dockerfile: kube/services/laravel/Dockerfile
    -
      image: mysql
      context: .
      docker:
        dockerfile: kube/services/mysql/Dockerfile
deploy:
  kubectl:
    manifests:
      - 'kube/services/*'
    flags:
      disableValidation: true
";
    expect(Service::toYaml())->toBe($expected);
});

it('does generate valid scaffold configuration without Dockerfile', function () {
    config(['app.name' => 'Laravel', 'kube.services.path' => 'kube/services']);

    Service::create('MySQL');

    $expected = "apiVersion: skaffold/v2beta24
kind: Config
metadata:
  name: laravel
build:
  artifacts: {  }
deploy:
  kubectl:
    manifests:
      - 'kube/services/*'
    flags:
      disableValidation: true
";
    expect(Service::toYaml())->toBe($expected);
});

it('can generate a valid kubernetes deployment', function () {
    $service = Service::create('Laravel')
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

    expect($service->getDeploymentManifest())->toBe($expected);
});

it('can generate a valid kubernetes deployment with replicas and namespace', function () {
    $service = Service::create('Laravel')
        ->setReplicaCount(2)
        ->setNamespace('production')
        ->setContainerPort(80);

    $expected = "apiVersion: apps/v1
kind: Deployment
metadata:
  name: laravel
  namespace: production
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
      namespace: production
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

    expect($service->getDeploymentManifest())->toBe($expected);
});

it('can generate a valid kubernetes deployment without ports', function () {
    $service = Service::create('Laravel');

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

    expect($service->getDeploymentManifest())->toBe($expected);
});

it('can generate a valid kubernetes deployment with manually setting image', function () {
    $service = Service::create('MySQL')
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

    expect($service->getDeploymentManifest())->toBe($expected);
});

it('can generate a valid kubernetes deployment with secrets', function () {
    $service = Service::create('MySQL')
        ->setEnvironmentVariable('MYSQL_ROOT_PASSWORD')
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
          env:
            -
              name: MYSQL_ROOT_PASSWORD
              valueFrom:
                secretKeyRef:
                  name: mysql
                  key: mysql_root_password
";

    expect($service->getDeploymentManifest())->toBe($expected);
});

it('can generate a valid kubernetes service', function () {
    $service = Service::create('MySQL')
        ->setContainerPort(3306)
        ->enableService();

    $expected = "apiVersion: v1
kind: Service
metadata:
  name: mysql
  namespace: default
spec:
  type: NodePort
  ports:
    -
      name: http
      port: 3306
      targetPort: 3306
  selector:
    app: mysql
";

    expect($service->getServiceManifest())->toBe($expected);
});

it('can generate a valid kubernetes service with service port', function () {
    $service = Service::create('MySQL')
        ->setContainerPort(3306)
        ->setServicePort(3000)
        ->enableService();

    $expected = "apiVersion: v1
kind: Service
metadata:
  name: mysql
  namespace: default
spec:
  type: NodePort
  ports:
    -
      name: http
      port: 3000
      targetPort: 3306
  selector:
    app: mysql
";

    expect($service->getServiceManifest())->toBe($expected);
});

it('can generate a valid kubernetes service with namespace', function () {
    $service = Service::create('MySQL')
        ->setNamespace('production')
        ->setContainerPort(3306)
        ->setServicePort(3000)
        ->enableService();

    $expected = "apiVersion: v1
kind: Service
metadata:
  name: mysql
  namespace: production
spec:
  type: NodePort
  ports:
    -
      name: http
      port: 3000
      targetPort: 3306
  selector:
    app: mysql
";

    expect($service->getServiceManifest())->toBe($expected);
});

it('enable service does generate a service manifest', function () {
    $service = Service::create('MySQL')
        ->setNamespace('production')
        ->setContainerPort(3306)
        ->setServicePort(3000)
        ->enableService();

    $expected = "apiVersion: v1
kind: Service
metadata:
  name: mysql
  namespace: production
spec:
  type: NodePort
  ports:
    -
      name: http
      port: 3000
      targetPort: 3306
  selector:
    app: mysql
";

    expect($service->getServiceManifest())->toBe($expected);
    expect($service->hasService())->toBeTrue();
});

it('throws an exception when generating a service manifest without a port', function () {
    $service = Service::create('MySQL')
        ->enableService();
    $service->getServiceManifest();
})->expectExceptionMessage('a service with type=NodePort must have a containerPort specified');

it('throws an exception when generating a service manifest when service is not enabled', function () {
    $service = Service::create('MySQL');
    $service->getServiceManifest();
})->expectExceptionMessage('you must call ->enableService() for a service manifest to be generated');