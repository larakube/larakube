<?php

namespace Larakube;

use Larakube\Cluster\Deployment;
use Larakube\Cluster\Resource;
use Larakube\Cluster\Resources;
use Larakube\Concerns\InteractsWithYAML;

class SkaffoldDumper
{
    use InteractsWithYAML;

    public function toYaml(): string
    {
        include_once sprintf('%s/kube/services.php', config('kube.project_root'));

        $artifacts = [];

        /** @var Deployment $deployment */
        foreach (Resources::getDeployments() as $deployment) {
            if (!$deployment->getDockerFilePath()) {
                continue;
            }

            $artifacts[] = [
                'image' => $deployment->getName(),
                'context' => '.',
                'docker' => [
                    'dockerfile' => sprintf('%s/%s/Dockerfile', config('kube.services.path'), $deployment->getName()),
                ],
            ];
        }

        return $this->arrayToYamlString(
            [
                'apiVersion' => 'skaffold/v2beta24',
                'kind' => 'Config',
                'metadata' => [
                    'name' => Resource::formatName(config('app.name', 'laravel')),
                ],
                'build' => [
                    'artifacts' => $artifacts,
                ],
                'deploy' => [
                    'kubectl' => [
                        'manifests' => [
                            sprintf('%s/*', config('kube.services.path')),
                        ],
                        'flags' => [
                            'disableValidation' => true,
                        ],
                    ],
                ],
            ]
        );
    }
}
