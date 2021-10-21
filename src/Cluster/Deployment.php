<?php

declare(strict_types=1);

namespace Larakube\Cluster;

use Larakube\Concerns\Cluster\HasEnvironmentVariables;
use Larakube\Enums\ResourceType;
use RenokiCo\PhpK8s\Instances\Container;
use RenokiCo\PhpK8s\Kinds\K8sDeployment;
use RenokiCo\PhpK8s\Kinds\K8sPod;

class Deployment extends Resource
{
    use HasEnvironmentVariables;

    public const TYPE = ResourceType::DEPLOYMENT;

    private function __construct(
        private string $name,
        private string $dockerFilePath = '',
        private string $namespace = 'default',
        private int $replicaCount = 1,
        private ?int $containerPort = null,
        private string $imageName = '',
        private string $imageTag = ''
    ) {
        $this->name = self::formatName($this->name);

        Resources::put(self::TYPE, $this);
    }

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDockerFilePath(): string
    {
        return $this->dockerFilePath;
    }

    public function setDockerFilePath(string $dockerPath): self
    {
        $this->dockerFilePath = $dockerPath;

        return $this;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getReplicaCount(): int
    {
        return $this->replicaCount;
    }

    public function setReplicaCount(int $replicaCount): self
    {
        $this->replicaCount = $replicaCount;

        return $this;
    }

    public function getContainerPort(): ?int
    {
        return $this->containerPort;
    }

    public function setContainerPort(int $containerPort): self
    {
        $this->containerPort = $containerPort;

        return $this;
    }

    public function getContainerImage(): string
    {
        if (trim($this->imageName) === '') {
            return '';
        }

        return sprintf('%s:%s', $this->imageName, $this->imageTag);
    }

    public function setContainerImage(string $image, string $tag = 'latest'): self
    {
        $this->imageName = $image;
        $this->imageTag  = $tag;

        return $this;
    }

    protected function makeResource(): array
    {
        $deployment = new K8sDeployment();
        $deployment->setName($this->getName());
        $deployment->setNamespace($this->getNamespace());
        $deployment->setReplicas($this->getReplicaCount());
        $deployment->setSelectors([
            'matchLabels' => [
                'app' => $this->getName(),
            ],
        ]);

        // create a pod template
        $pod = new K8sPod();
        $pod->setName($this->getName());
        $pod->setNamespace($this->getNamespace());
        $pod->setLabels(['app' => $this->getName()]);

        // create a container spec
        $container = new Container();
        $container->setAttribute('name', $this->getName());

        if ($this->imageName) {
            $container->setImage($this->imageName, $this->imageTag ?? 'latest');
        } else {
            $container->setAttribute('image', $this->getName());
        }
        $container->setAttribute('imagePullPolicy', 'IfNotPresent');

        if ($this->getContainerPort() !== null) {
            $container->addPort($this->getContainerPort(), 'TCP', 'http');
        }

        // set environment variables from secrets
        if (isset($this->environmentVariables) && $this->environmentVariables->isNotEmpty()) {
            $container->setAttribute(
                'env',
                $this->environmentVariables->values()->map(function (EnvironmentVariable $environmentVariable) {
                    return [
                        'name' => $environmentVariable->getName(),
                        'valueFrom' => [
                            'secretKeyRef' => [
                                'name' => $this->getName(),
                                'key' => $environmentVariable->getName(),
                            ],
                        ],
                    ];
                })->toArray()
            );
        }

        $pod->setContainers([$container]);
        $deployment->setTemplate($pod);

        return $deployment->toArray();
    }
}
