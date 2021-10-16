<?php

namespace Larakube\BuildProcess;

use Illuminate\Support\Facades\File;
use Larakube\Service;
use Symfony\Component\Process\Process;

class BuildAndDeploy extends Step
{
    private string $skaffoldBinary;

    public function __construct()
    {
        $this->skaffoldBinary = LARAKUBE_ROOT . '/bin/skaffold/macos/skaffold';

        $this->generateSkaffoldConfiguration();
    }

    public function __invoke()
    {
        $process = new Process([
                $this->skaffoldBinary,
                'run',
                '--auto-create-config=false',
                sprintf('-d=%s', config('kube.registry.base_url')),
                sprintf('-f=%s', base_path('skaffold.yaml')),
            ]
        );
        $process->setTimeout(60 * 10);

        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->output->error($buffer);
            } else {
                $this->output->write($buffer);
            }
        });
    }

    private function generateSkaffoldConfiguration(): void
    {
        include_once base_path('kube/services.php');

        File::put(base_path('skaffold.yaml'), Service::toYaml());
    }
}