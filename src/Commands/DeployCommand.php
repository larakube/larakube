<?php

namespace Larakube\Commands;

use Illuminate\Console\Command;
use Larakube\BuildProcess\BuildAndDeploy;
use Larakube\BuildProcess\EnsureEnvironmentSecrets;
use Larakube\BuildProcess\Step;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{
    protected $signature = 'kube:deploy';

    protected $description = 'Deploy the application to a Kubernetes cluster.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        collect([
            new EnsureEnvironmentSecrets(),
            new BuildAndDeploy(),
        ])->each(function (Step $step) use ($output) {
            $step->setOutput($this->getOutput());
            $step();
        });

        return self::SUCCESS;
    }
}