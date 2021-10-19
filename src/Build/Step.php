<?php

namespace Larakube\Build;

use Illuminate\Console\OutputStyle;

abstract class Step
{
    protected OutputStyle $output;

    public function setOutput(OutputStyle $output): self
    {
        $this->output = $output;

        return $this;
    }

    public abstract function __invoke(): void;
}
