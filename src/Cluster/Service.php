<?php

declare(strict_types=1);

namespace Larakube\Cluster;

use Larakube\Enums\CloudProviderType;
use Larakube\Enums\ResourceType;
use Larakube\Enums\ServiceKindType;
use RenokiCo\PhpK8s\Kinds\K8sService;

class Service extends Resource
{
    public const TYPE = ResourceType::SERVICE;

    private function __construct(
        private string $name,
        private string $namespace = 'default',
        private string $kind = 'NodePort',
        private int $servicePort = 80,
        private int $targetPort = 80,
        private array $annotations = []
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

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function loadBalancer(): self
    {
        $this->kind = ServiceKindType::LOAD_BALANCER;

        return $this;
    }

    public function getServicePort(): int
    {
        return $this->servicePort;
    }

    public function setServicePort(int $servicePort): self
    {
        $this->servicePort = $servicePort;

        return $this;
    }

    public function getTargetPort(): int
    {
        return $this->targetPort;
    }

    public function setTargetPort(int $targetPort): self
    {
        $this->targetPort = $targetPort;

        return $this;
    }

    protected function makeResource(): array
    {
        $this->makeAnnotations();

        $service = new K8sService();
        $service->setName($this->getName());
        $service->setNamespace($this->getNamespace());
        $service->setSpec('type', $this->getKind());
        $service->addPort([
            'name' => 'http',
            'port' => $this->getServicePort(),
            'targetPort' => $this->getTargetPort(),
        ]);
        $service->setSelectors(['app' => $this->getName()]);

        if ($this->annotations) {
            $service->setAnnotations($this->annotations);
        }

        return $service->toArray();
    }

    private function makeAnnotations(): void
    {
        if (config('kube.cloud_provider') === CloudProviderType::DIGITAL_OCEAN) {
            if ($this->getKind() === ServiceKindType::LOAD_BALANCER) {
                $this->annotations['service.beta.kubernetes.io/do-loadbalancer-protocol'] = 'http';
            }
        }
    }
}
