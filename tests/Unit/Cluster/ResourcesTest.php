<?php

use Larakube\Cluster\Deployment;
use Larakube\Cluster\Ingress;
use Larakube\Cluster\Resources;
use Larakube\Enums\ResourceType;

beforeEach(fn() => Resources::reset());

it('put and get a ingress resource', function () {
    Resources::put(
        ResourceType::INGRESS,
        Ingress::create('ingress', 'laravel')
    );

    $ingresses = Resources::getIngresses();
    expect($ingresses)->toHaveCount(1);
    expect($ingresses->get('ingress'))->not()->toBeNull();
});

it('put and get a deployment resource', function () {
    Resources::put(
        ResourceType::DEPLOYMENT,
        Deployment::create('laravel')
    );

    $deployments = Resources::getDeployments();
    expect($deployments)->toHaveCount(1);
    expect($deployments->get('laravel'))->not()->toBeNull();
});