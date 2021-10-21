<?php

namespace Larakube\Concerns\Cluster;

use Illuminate\Support\Collection;
use Larakube\Cluster\EnvironmentVariable;

trait HasEnvironmentVariables
{
    /**
     * @var Collection<string, EnvironmentVariable>
     */
    private Collection $environmentVariables;

    public function setEnvironmentVariable(EnvironmentVariable $environmentVariable): self
    {
        if (!isset($this->environmentVariables)) {
            $this->environmentVariables = collect();
        }

        $this->environmentVariables->put(
            $environmentVariable->getName(),
            $environmentVariable
        );

        return $this;
    }

    public function getEnvironmentVariables(): Collection
    {
        return $this->environmentVariables;
    }
}