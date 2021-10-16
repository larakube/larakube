<?php

namespace Larakube;

use Illuminate\Support\ServiceProvider;
use Larakube\Commands\DeployCommand;
use Larakube\Commands\InstallCommand;
use Larakube\Commands\MakeCommand;

class LarakubeServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
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
    }
}