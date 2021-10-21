<?php

namespace Larakube\Cluster;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Larakube\Concerns\InteractsWithYAML;

abstract class Resource implements Arrayable
{
    use InteractsWithYAML;

    public const TYPE = '';

    abstract protected function makeResource(): array;

    abstract public function getName(): string;

    public static function formatName(string $name): string
    {
        return Str::kebab(Str::lower($name));
    }

    public function toManifest(): string
    {
        return $this->arrayToYamlString($this->toArray());
    }

    public function toArray(): array
    {
        return $this->makeResource();
    }

    public function flush(string $directoryPath): void
    {
        if (static::TYPE === '') {
            throw new Exception('type has not been set on ' . static::class);
        }

        File::put(
            sprintf('%s/%s.yaml', $directoryPath, Str::lower(static::TYPE)),
            $this->toManifest()
        );
    }
}