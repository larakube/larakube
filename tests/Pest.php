<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use RenokiCo\PhpK8s\Kinds\K8sDeployment;
use RenokiCo\PhpK8s\Kinds\K8sService;
use RenokiCo\PhpK8s\KubernetesCluster;
use Symfony\Component\Process\Process;

uses(Tests\TestCase::class)->in('Feature', 'Unit');

const TEMP_DIR           = LARAKUBE_ROOT . '/tests/temp/';
const TEST_SERVICES_PATH = LARAKUBE_ROOT . '/tests/data/services/';

function startDockerRegistryContainer(): void
{
    $process = Process::fromShellCommandline(
        'docker run -d --name=registry -it --network=host alpine ash -c "apk add socat && socat TCP-LISTEN:5000,reuseaddr,fork TCP:$(minikube ip):5000"'
    );
    handleProcess($process);
}

function cleanupDockerRegistryContainer(): void
{
    $process = Process::fromShellCommandline(
        'docker container stop registry || true && docker container rm registry || true'
    );
    handleProcess($process);
}

function handleProcess(Process $process): void
{
    $code = $process->run();
    if ($code > 0) {
        echo $process->getErrorOutput() . PHP_EOL;
        expect($code)->toBeEmpty();
    }
    $process->wait();
}

function generateKubeConfig(): void
{
    $process = Process::fromShellCommandline(
        'KUBECONFIG="tests/data/kube/config" minikube update-context'
    );
    handleProcess($process);
}

function deleteAllKubernetesResources(): void
{
    $cluster = KubernetesCluster::fromKubeConfigYamlFile('tests/data/kube/config', 'minikube');
    $cluster->getAllDeployments()->each(fn(K8sDeployment $deployment) => $deployment->delete());
    $cluster->getAllServices()->each(fn(K8sService $service) => $service->delete());

    $totalResources = 1;
    while ($totalResources > 0) {
        $totalResources = count($cluster->getAllDeployments()) + count($cluster->getAllServices());
    }
}