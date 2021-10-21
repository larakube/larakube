<?php

use Larakube\Cluster\Ingress;

it('does generate valid manifest', function () {
    config(['app.name' => 'Laravel', 'kube.services.path' => 'kube/services']);

    $ingress = Ingress::create('laravel-ingress', 'laravel');

    $expected = "apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: laravel-ingress
  annotations:
    ingress.kubernetes.io/rewrite-target: /
  namespace: default
spec:
  rules:
    -
      http:
        paths:
          -
            path: /
            pathType: Prefix
            backend:
              service:
                name: laravel
                port:
                  number: 80
";
    expect($ingress->toManifest())->toBe($expected);
});

it('does generate valid manifest with set rewrite target', function () {
    config(['app.name' => 'Laravel', 'kube.services.path' => 'kube/services']);

    $ingress = Ingress::create('laravel', 'laravel')
        ->setRewriteTarget('');

    $expected = "apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: laravel
  namespace: default
spec:
  rules:
    -
      http:
        paths:
          -
            path: /
            pathType: Prefix
            backend:
              service:
                name: laravel
                port:
                  number: 80
";
    expect($ingress->toManifest())->toBe($expected);
});

it('does generate valid manifest with set port', function () {
    config(['app.name' => 'Laravel', 'kube.services.path' => 'kube/services']);

    $ingress = Ingress::create('laravel', 'laravel')
        ->setServicePort(8000);

    $expected = "apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: laravel
  annotations:
    ingress.kubernetes.io/rewrite-target: /
  namespace: default
spec:
  rules:
    -
      http:
        paths:
          -
            path: /
            pathType: Prefix
            backend:
              service:
                name: laravel
                port:
                  number: 8000
";
    expect($ingress->toManifest())->toBe($expected);
});
