<?php

namespace Larakube\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Larakube\Cluster\Resource;
use Larakube\Cluster\Resources;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command
{
    protected $signature = 'kube:make';

    protected $description = 'Generate the service folder and manifests automatically.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        include_once sprintf('%s/kube/services.php', config('kube.project_root'));

        /** @var Resource $resource */
        foreach (Resources::all() as $resource) {
            $servicePath = sprintf('%s/%s', config('kube.services.path'), $resource->getName());
            if (!File::exists($servicePath)) {
                File::ensureDirectoryExists($servicePath);
            }

            $resource->flush($servicePath);
        }

        return self::SUCCESS;
    }
}
