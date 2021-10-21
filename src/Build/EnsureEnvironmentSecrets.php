<?php

namespace Larakube\Build;

use Larakube\Cluster\Deployment;
use Larakube\Cluster\EnvironmentVariable;
use Larakube\Cluster\Resources;
use RenokiCo\PhpK8s\KubernetesCluster;

class EnsureEnvironmentSecrets extends Step
{
    private KubernetesCluster $cluster;

    public function __construct()
    {
        include_once sprintf('%s/kube/services.php', config('kube.project_root'));

        $this->cluster = KubernetesCluster::fromKubeConfigVariable();
    }

    public function __invoke(): void
    {
        Resources::getDeployments()->each(function (Deployment $deployment) {
            $this->applyClusterSecrets($deployment);
        });
    }

    private function applyClusterSecrets(Deployment $deployment): void
    {
        $this->cluster->secret()
            ->setName($deployment->getName())
            ->setData(
                collect($deployment->getEnvironmentVariables())->mapWithKeys(
                    function (EnvironmentVariable $environmentVariable) {
                        return [
                            $environmentVariable->getName() => $environmentVariable->getValue(),
                        ];
                    }
                )->toArray()
            )->createOrUpdate();
    }
}
