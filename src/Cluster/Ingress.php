<?php

declare(strict_types=1);

namespace Larakube\Cluster;

use Larakube\Enums\ResourceType;
use RenokiCo\PhpK8s\Kinds\K8sIngress;

class Ingress extends Resource
{
    public const TYPE = ResourceType::INGRESS;

    private function __construct(
        private string $name,
        private string $serviceName,
        private int $servicePort = 80,
        private string $rewriteTarget = '/'
    ) {
        $this->name = self::formatName($this->name);

        Resources::put(self::TYPE, $this);
    }

    public static function create(string $name, string $serviceName): self
    {
        return new self($name, $serviceName);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setServicePort(int $servicePort): self
    {
        $this->servicePort = $servicePort;

        return $this;
    }

    public function setRewriteTarget(string $target = '/'): self
    {
        $this->rewriteTarget = $target;

        return $this;
    }

    protected function makeResource(): array
    {
        $ingress = new K8sIngress();
        $ingress->setName($this->name);

        $annotations = [];
        if ($this->rewriteTarget) {
            $annotations['ingress.kubernetes.io/rewrite-target'] = $this->rewriteTarget;
        }

        if ($annotations) {
            $ingress->setAnnotations($annotations);
        }

        $ingress->setRules([
            [
                'http' => [
                    'paths' => [
                        [
                            'path' => '/',
                            'pathType' => 'Prefix',
                            'backend' => [
                                'service' => [
                                    'name' => $this->serviceName,
                                    'port' => [
                                        'number' => $this->servicePort,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return $ingress->toArray();
    }
}
