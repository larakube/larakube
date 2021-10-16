<?php

namespace Larakube\BuildProcess;

use Larakube\Service;
use RenokiCo\PhpK8s\KubernetesCluster;

class EnsureEnvironmentSecrets extends Step
{
    private KubernetesCluster $cluster;

    public function __construct()
    {
        include_once base_path('kube/services.php');

        $this->cluster = KubernetesCluster::fromKubeConfigVariable();
    }

    public function __invoke()
    {
        Service::all()->each(function (Service $service) {
            foreach ($service->getEnvironmentVariables() as $secretReference => $secret) {
                $this->cluster->secret()
                    ->setName($secretReference)
                    ->setData(
                        collect($secret)->mapWithKeys(function ($secret) {
                            return [
                                $secret['key'] => env(
                                    $secret['fromEnvName'] ?: $secret['name']
                                ),
                            ];
                        })->toArray()
                    )->createOrUpdate();
            }
        });
    }
}