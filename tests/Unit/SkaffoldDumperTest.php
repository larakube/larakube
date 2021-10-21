<?php

use Larakube\SkaffoldDumper;

it('does generate valid scaffold configuration', function () {
    config([
        'kube.project_root' => package_root(''),
        'kube.services.path' => 'kube/services',
    ]);

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
    expect((new SkaffoldDumper())->toYaml())->toBe($expected);
});