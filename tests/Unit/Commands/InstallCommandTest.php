<?php

it('installs Skaffold for MacOS', function () {
    $this->artisan('kube:install')->assertExitCode(0);
});
