<?php

namespace Larakube\Cluster;

class EnvironmentVariable
{
    public function __construct(
        private string $name,
        private string $value = '',
        private string $fromEnvironmentVariableName = ''
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        if ($this->fromEnvironmentVariableName) {
            if ($resolved = (env($this->fromEnvironmentVariableName))) {
                return $resolved;
            }
        }
        return value($this->value);
    }

    public function getFromEnvironmentVariable(): string
    {
        return $this->fromEnvironmentVariableName;
    }

    public function setFromEnvironmentVariable(string $name): self
    {
        $this->fromEnvironmentVariableName = $name;

        return $this;
    }
}