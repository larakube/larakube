<?php

namespace Larakube\Commands;

use Illuminate\Console\Command;
use Larakube\Build\BuildAndDeploy;
use Larakube\Build\EnsureEnvironmentSecrets;
use Larakube\Build\Step;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{
    protected $signature = 'kube:deploy';

    protected $description = 'Deploy the application to a Kubernetes cluster.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        collect([
            app()->make(EnsureEnvironmentSecrets::class),
            app()->make(BuildAndDeploy::class),
        ])->each(function (Step $step) {
            $step->setOutput($this->getOutput());
            $step();
        });

        return self::SUCCESS;
    }
}