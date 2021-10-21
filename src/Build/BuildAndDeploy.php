<?php

namespace Larakube\Build;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Larakube\SkaffoldDumper;
use Symfony\Component\Process\Process;

class BuildAndDeploy extends Step
{
    private string $skaffoldBinary;

    public function __invoke(): void
    {
        $this->ensureSkaffoldExists();
        $this->generateSkaffoldConfiguration();

        /** @var Process $process */
        $process = app()->make(
            Process::class,
            [
                $this->skaffoldBinary,
                'run',
                '--auto-create-config=false',
                sprintf('-d=%s', config('kube.registry.base_url')),
                sprintf('-f=%s', package_root('skaffold.yaml')),
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

    private function ensureSkaffoldExists(): void
    {
        $this->skaffoldBinary = LARAKUBE_ROOT . '/bin/skaffold';

        if (!File::exists($this->skaffoldBinary)) {
            if (Artisan::call('kube:install') > 0) {
                throw new Exception('failed to install Skaffold');
            }
        }
    }

    private function generateSkaffoldConfiguration(): void
    {
        include_once sprintf('%s/kube/services.php', config('kube.project_root'));

        File::put(package_root('skaffold.yaml'), (new SkaffoldDumper())->toYaml());
    }
}
