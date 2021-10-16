<?php

namespace Larakube\BuildProcess;

use Illuminate\Console\OutputStyle;

abstract class Step
{
    protected const CHECK = '\xE2\x9C\x94';

    protected OutputStyle $output;

    public function setOutput(OutputStyle $output): self
    {
        $this->output = $output;

        return $this;
    }

    public abstract function __invoke();
}