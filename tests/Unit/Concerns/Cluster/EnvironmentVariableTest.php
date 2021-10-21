<?php

use Larakube\Cluster\EnvironmentVariable;

test('setters and getters', function () {
    $environment = new EnvironmentVariable('PASSWORD', 'secret');

    expect($environment->getName())->toBe('PASSWORD');
    expect($environment->getValue())->toBe('secret');
    expect($environment->getFromEnvironmentVariable())->toBe('');
});

test('returns the value from current environment', function () {
    putenv('TEST_PASSWORD=hello');

    $environment = new EnvironmentVariable('PASSWORD', fromEnvironmentVariableName: 'TEST_PASSWORD');

    expect($environment->getName())->toBe('PASSWORD');
    expect($environment->getValue())->toBe('hello');
    expect($environment->getFromEnvironmentVariable())->toBe('TEST_PASSWORD');
});

test('returns the fallback value as environment variable did not exist', function () {
    putenv('TEST_PASSWORD=');
    $environment = new EnvironmentVariable('PASSWORD', 'secret', 'TEST_PASSWORD');

    expect($environment->getName())->toBe('PASSWORD');
    expect($environment->getValue())->toBe('secret');
    expect($environment->getFromEnvironmentVariable())->toBe('TEST_PASSWORD');
});