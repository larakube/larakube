<?php

use Illuminate\Support\Facades\File;

afterEach(fn() => cleanup());

function cleanup(): void
{
    File::deleteDirectories(package_root('tests/temp'));
}

it('creates new directory and writes manifests', function () {
    config([
        'kube.project_root' => package_root(),
        'kube.services.path' => package_root('tests/temp'),
    ]);

    $this->artisan('kube:make')->assertExitCode(0);

    expect(File::exists(package_root('tests/temp/laravel')))->toBeTrue();
    expect(File::exists(package_root('tests/temp/laravel/deployment.yaml')))->toBeTrue();
    expect(File::exists(package_root('tests/temp/laravel/ingress.yaml')))->toBeTrue();
    expect(File::exists(package_root('tests/temp/mysql')))->toBeTrue();
    expect(File::exists(package_root('tests/temp/mysql/deployment.yaml')))->toBeTrue();
});
