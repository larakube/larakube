<?php

namespace Larakube;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Larakube\Concerns\InteractsWithYAML;
use RenokiCo\PhpK8s\Instances\Container;
use RenokiCo\PhpK8s\Kinds\K8sDeployment;
use RenokiCo\PhpK8s\Kinds\K8sPod;
use RenokiCo\PhpK8s\Kinds\K8sService;
use Symfony\Component\Yaml\Yaml;

class Service
{
    use InteractsWithYAML;

    private static array $services = [];

    private string $name;

    private string $dockerfilePath = '';

    private string $namespace = 'default';

    private int $replicas = 1;

    private ?int $containerPort = null;

    private ?int $servicePort = null;

    private string $containerImage = '';

    private string $containerImageTag = '';

    private bool $hasService = false;

    private array $environment = [];

    private function __construct(string $name)
    {
        $this->name = self::formatName($name);

        if (self::exists($this->name)) {
            throw new Exception('service already exists');
        }

        self::$services[$this->name] = $this;
    }

    public static function reset(): void
    {
        self::$services = [];
    }

    public static function create(string $name): Service
    {
        return new self($name);
    }

    public static function get(string $name): Service
    {
        $name = self::formatName($name);

        if (self::exists($name)) {
            return self::$services[$name];
        }

        throw new Exception('service not found');
    }

    public static function all(): Collection
    {
        return collect(self::$services);
    }

    private static function exists(string $formattedName): bool
    {
        return array_key_exists($formattedName, self::$services);
    }

    private static function formatName(string $name): string
    {
        return Str::camel(Str::lower($name));
    }

    public static function make(): void
    {
        /** @var Service $service */
        foreach (self::$services as $serviceName => $service) {
            $servicePath = sprintf('%s/%s', config('kube.services.path'), $service->getName());
            if (!File::exists($servicePath)) {
                File::ensureDirectoryExists($servicePath);
            }

            File::put(sprintf('%s/deployment.yaml', $servicePath), $service->getDeploymentManifest());
            if ($service->hasService()) {
                File::put(sprintf('%s/service.yaml', $servicePath), $service->getServiceManifest());
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDockerfile(string $relativePath): self
    {
        $this->dockerfilePath = $relativePath;

        return $this;
    }

    public function getDockerfilePath(): string
    {
        return $this->dockerfilePath;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function setReplicaCount(int $replicas = 1): self
    {
        $this->replicas = $replicas;

        return $this;
    }

    public function setContainerPort(int $containerPort): self
    {
        $this->containerPort = $containerPort;

        return $this;
    }

    public function setServicePort(int $servicePort): self
    {
        $this->servicePort = $servicePort;

        return $this;
    }

    public function setContainerImage(string $image, string $tag = 'latest'): self
    {
        $this->containerImage    = $image;
        $this->containerImageTag = $tag;

        return $this;
    }

    public function enableService(): self
    {
        $this->hasService = true;

        return $this;
    }

    public function hasService(): bool
    {
        return $this->hasService;
    }

    public function setEnvironmentVariable(string $name, string $value = '', string $fromEnvName = ''): self
    {
        $this->environment[] = [
            'name' => $name,
            'fromEnvName' => $fromEnvName ?? $name,
            'serviceName' => $this->name,
            'key' => Str::lower($name),
            'type' => $value === '' ? 'dynamic' : 'raw',
            'value' => $value,
        ];

        return $this;
    }

    public function getEnvironmentVariablesByService(): array
    {
        $secrets = [];

        collect($this->environment)->each(function (array $environment) use (&$secrets) {
            $secrets[$environment['serviceName']][] = $environment;
        });

        return $secrets;
    }

    public static function toYaml(): string
    {
        $artifacts = [];

        /** @var Service $service */
        foreach (self::$services as $formattedName => $service) {
            if (!$service->getDockerfilePath()) {
                continue;
            }

            $artifacts[] = [
                'image' => $formattedName,
                'context' => '.',
                'docker' => [
                    'dockerfile' => sprintf('%s/%s/Dockerfile', config('kube.services.path'), $formattedName),
                ],
            ];
        }

        return Yaml::dump(
            [
                'apiVersion' => 'skaffold/v2beta24',
                'kind' => 'Config',
                'metadata' => [
                    'name' => self::formatName(config('app.name', 'laravel')),
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
            ],
            8,
            2
        );
    }

    public function getDeploymentManifest(): string
    {
        // create the deployment manifest
        $deployment = new K8sDeployment();
        $deployment->setName($this->name);
        $deployment->setNamespace($this->namespace);
        $deployment->setReplicas($this->replicas);
        $deployment->setSelectors([
            'matchLabels' => [
                'app' => $this->name,
            ],
        ]);

        // create a pod template
        $pod = new K8sPod();
        $pod->setName($this->name);
        $pod->setNamespace($this->namespace);
        $pod->setLabels(['app' => $this->name]);

        // create a container spec
        $container = new Container();
        $container->setAttribute('name', $this->name);

        if ($this->containerImage) {
            $container->setImage($this->containerImage, $this->containerImageTag);
        } else {
            $container->setAttribute('image', $this->name);
        }
        $container->setAttribute('imagePullPolicy', 'IfNotPresent');

        if ($this->containerPort) {
            $container->addPort($this->containerPort, 'TCP', 'http');
        }

        // secrets
        if (!empty($this->environment)) {
            $container->setAttribute(
                'env',
                collect($this->environment)->map(function (array $secret) {
                    return [
                        'name' => $secret['name'],
                        'valueFrom' => [
                            'secretKeyRef' => [
                                'name' => $secret['serviceName'],
                                'key' => $secret['key'],
                            ],
                        ],
                    ];
                })->toArray()
            );
        }

        $pod->setContainers([$container]);
        $deployment->setTemplate($pod);

        return $this->arrayToYamlString($deployment->toArray());
    }

    public function getServiceManifest(): string
    {
        if (!$this->hasService) {
            throw new Exception('you must call ->enableService() for a service manifest to be generated');
        }
        if (!$this->containerPort) {
            throw new Exception('a service with type=NodePort must have a containerPort specified');
        }

        $service = new K8sService();
        $service->setName($this->name);
        $service->setNamespace($this->namespace);
        $service->setSpec('type', 'NodePort');
        $service->addPort([
            'name' => 'http',
            'port' => $this->servicePort ?? $this->containerPort,
            'targetPort' => $this->containerPort,
        ]);
        $service->setSelectors(['app' => $this->name]);

        return $this->arrayToYamlString($service->toArray());
    }
}
