<?php

declare(strict_types=1);

namespace Larakube\Cluster;

use Exception;
use Illuminate\Support\Collection;
use Larakube\Enums\ResourceType;

class Resources
{
    private static array $ingresses = [];

    private static array $deployments = [];

    private static array $services = [];

    public static function reset(): void
    {
        self::$ingresses   = [];
        self::$deployments = [];
        self::$services    = [];
    }

    public static function put(string $type, Resource $resource, string $name = ''): void
    {
        $name = $resource->getName() ?? Resource::formatName($name);

        match ($type) {
            ResourceType::INGRESS => self::$ingresses[$name] = $resource,
            ResourceType::DEPLOYMENT => self::$deployments[$name] = $resource,
            ResourceType::SERVICE => self::$services[$name] = $resource,
            default => throw new Exception('resource is not supported')
        };
    }

    public static function getIngresses(): Collection
    {
        return collect(self::$ingresses);
    }

    public static function getDeployments(): Collection
    {
        return collect(self::$deployments);
    }

    public static function getServices(): Collection
    {
        return collect(self::$services);
    }

    public static function all(): Collection
    {
        return collect([
            ...self::getIngresses()->values(),
            ...self::getDeployments()->values(),
            ...self::getServices()->values(),
        ]);
    }
}
