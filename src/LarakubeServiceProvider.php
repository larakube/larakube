<?php

namespace Larakube;

use Illuminate\Support\ServiceProvider;
use Larakube\Build\BuildAndDeploy;
use Larakube\Build\EnsureEnvironmentSecrets;
use Larakube\Commands\DeployCommand;
use Larakube\Commands\InstallCommand;
use Larakube\Commands\MakeCommand;
use Symfony\Component\Process\Process;

class LarakubeServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../kube' => base_path('kube'),
            __DIR__ . '/../config/kube.php' => config_path('kube.php'),
        ], 'kube');

        if ($this->app->runningInConsole()) {
            $this->providers();
            $this->commands([
                InstallCommand::class,
                DeployCommand::class,
                MakeCommand::class,
            ]);
        }
    }

    private function providers(): void
    {
        $this->app->bind(Process::class, function ($app, $args) {
            return new Process($args);
        });

        $this->app->bind(EnsureEnvironmentSecrets::class, function () {
            return new EnsureEnvironmentSecrets();
        });

        $this->app->bind(BuildAndDeploy::class, function () {
            return new BuildAndDeploy();
        });
    }
}