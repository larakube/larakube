<?php

namespace Larakube\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    protected $signature = 'kube:install';

    protected $description = 'Install the dependencies for Larakube.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (File::exists(package_root('bin/skaffold'))) {
            File::delete(package_root('bin/skaffold'));
        }

        File::ensureDirectoryExists(package_root('bin'));

        if (PHP_OS === 'Darwin') {
            $process = Process::fromShellCommandline(
                'curl -Lo skaffold https://storage.googleapis.com/skaffold/releases/latest/skaffold-darwin-amd64'
            )->setWorkingDirectory(package_root('bin'));
        } elseif (PHP_OS === 'Linux') {
            $process = Process::fromShellCommandline(
                'curl -Lo skaffold https://storage.googleapis.com/skaffold/releases/latest/skaffold-linux-amd64'
            )->setWorkingDirectory(package_root('bin'));
        }
        if (!isset($process)) {
            $this->error('Larakube can only run on Linux or Mac OS.');
            return self::FAILURE;
        }

        if ($process->run() > 0) {
            $this->error($process->getErrorOutput());
            return self::FAILURE;
        }
        chmod(package_root('bin/skaffold'), 0755);

        return self::SUCCESS;
    }
}