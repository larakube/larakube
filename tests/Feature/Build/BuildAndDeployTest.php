<?php

use Illuminate\Support\Facades\File;
use Larakube\Build\BuildAndDeploy;
use Larakube\Build\EnsureEnvironmentSecrets;
use RenokiCo\PhpK8s\Kinds\K8sSecret;
use RenokiCo\PhpK8s\KubernetesCluster;
use Symfony\Component\Process\Process;

beforeEach(function () {
    startDockerRegistryContainer();
});

afterEach(function () {
    cleanupDockerRegistryContainer();
});

it('ensures skaffold binary is installed if not exists and generates skaffold configuration', function () {
    config([
        'kube.project_root' => package_root(),
        'kube.services.path' => 'kube/services',
    ]);

    File::delete(package_root('bin/skaffold'));

    $this->mock(EnsureEnvironmentSecrets::class)
        ->shouldReceive('setOutput')
        ->once()
        ->passThru()
        ->shouldReceive('__invoke')
        ->once();

    $this->partialMock(BuildAndDeploy::class)
        ->shouldReceive('setOutput')
        ->once()
        ->passThru();

    // mock process
    $process = $this->mock(Symfony\Component\Process\Process::class, function ($mock) {
        $mock->shouldReceive('setTimeout')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('run')
            ->once()
            ->andReturn(0);
    });
    $this->app->bind(Process::class, function () use ($process) {
        return $process;
    });

    $this->artisan('kube:deploy')->assertExitCode(0);

    expect(File::exists(package_root('bin/skaffold')))->toBeTrue();
    expect(File::exists(package_root('skaffold.yaml')))->toBeTrue();
});

test('it deploys a fresh application to cluster', function () {
    deleteAllKubernetesResources();
    config([
        'kube.project_root' => package_root(),
        'kube.services.path' => 'kube/services',
        'kube.registry.base_url' => 'localhost:5000',
    ]);
    putenv('DB_DATABASE=test');
    putenv('DB_PASSWORD=test');

    $this->artisan('kube:deploy')->assertExitCode(0);

    $cluster = KubernetesCluster::fromKubeConfigVariable('minikube');

    // assert secrets were created
    $secretCountIncludingDefault = 3;
    $secrets                     = $cluster->getAllSecrets();
    expect($secrets)->toHaveCount($secretCountIncludingDefault);
    expect($secrets->filter(fn(K8sSecret $secret) => $secret->getName() === 'database'))->not()->toBeNull();
    expect($secrets->filter(fn(K8sSecret $secret) => $secret->getName() === 'laravel'))->not()->toBeNull();

    // assert deployments were created
    $expectedDeployments = 2;
    $deployments         = $cluster->getAllDeployments();
    expect($deployments)->toHaveCount($expectedDeployments);

    // We only have two services but there is a default
    // kubernetes service.
    expect($cluster->getAllServices())->toHaveCount(3);
});