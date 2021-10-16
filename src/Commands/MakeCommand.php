<?php

namespace Larakube\Commands;

use Illuminate\Console\Command;
use Larakube\Service;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command
{
    protected $signature = 'kube:make';

    protected $description = 'Generate the service folder and manifests automatically.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        include_once base_path('kube/services.php');

        Service::make();

        return self::SUCCESS;
    }
}