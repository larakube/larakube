<?php

namespace Larakube\Build;

use Larakube\Service;
use RenokiCo\PhpK8s\KubernetesCluster;

class EnsureEnvironmentSecrets extends Step
{
    private KubernetesCluster $cluster;

    public function __construct()
    {
        include_once package_root('kube/services.php');

        $this->cluster = KubernetesCluster::fromKubeConfigVariable();
    }

    public function __invoke(): void
    {
        Service::all()->each(function (Service $service) {
            foreach ($service->getEnvironmentVariablesByService() as $serviceName => $secret) {
                $this->cluster->secret()
                    ->setName($serviceName)
                    ->setData(
                        collect($secret)->mapWithKeys(function ($secret) {
                            return [
                                $secret['key'] => self::lookupEnvironmentValue($secret),
                            ];
                        })->toArray()
                    )->createOrUpdate();
            }
        });
    }

    private static function lookupEnvironmentValue(array $secret): string
    {
        if ($secret['type'] === 'raw') {
            return value($secret['value']);
        }

        return env(
            $secret['fromEnvName'] ?: $secret['name'],
            $secret['value']
        );
    }
}
