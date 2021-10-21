<?php

use Larakube\Cluster\Service;
use Larakube\Enums\CloudProviderType;

test('default setters and getters ', function () {
    $service = Service::create('laravel');

    expect($service->getName())->toBe('laravel');
    expect($service->getNamespace())->toBe('default');
    expect($service->getServicePort())->toBe(80);
    expect($service->getTargetPort())->toBe(80);
});

test('setters and getters ', function () {
    $service = Service::create('mysql')
        ->setNamespace('production')
        ->setServicePort(3306)
        ->setTargetPort(3306);

    expect($service->getName())->toBe('mysql');
    expect($service->getNamespace())->toBe('production');
    expect($service->getServicePort())->toBe(3306);
    expect($service->getTargetPort())->toBe(3306);
});

it('generates a valid service manifest ', function () {
    $service = Service::create('mysql')
        ->setServicePort(3306)
        ->setTargetPort(3306);

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

    expect($service->toManifest())->toBe($expected);
});

it('generates a valid service manifest with namespace ', function () {
    $service = Service::create('mysql')
        ->setNamespace('production')
        ->setServicePort(3306)
        ->setTargetPort(3306);

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
      port: 3306
      targetPort: 3306
  selector:
    app: mysql
";

    expect($service->toManifest())->toBe($expected);
});

it('[DO] generates a valid service manifest with load balancer ', function () {
    config(['kube.cloud_provider' => CloudProviderType::DIGITAL_OCEAN]);

    $service = Service::create('mysql')
        ->setServicePort(3306)
        ->setTargetPort(3306)
        ->loadBalancer();

    $expected = "apiVersion: v1
kind: Service
metadata:
  name: mysql
  namespace: default
  annotations:
    service.beta.kubernetes.io/do-loadbalancer-protocol: http
spec:
  type: LoadBalancer
  ports:
    -
      name: http
      port: 3306
      targetPort: 3306
  selector:
    app: mysql
";

    expect($service->toManifest())->toBe($expected);
});