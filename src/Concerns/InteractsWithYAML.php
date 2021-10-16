<?php

namespace Larakube\Concerns;

use Symfony\Component\Yaml\Yaml;

trait InteractsWithYAML
{
    public function arrayToYamlString(array $array): string
    {
        return Yaml::dump(
            $array,
            10,
            2
        );
    }
}