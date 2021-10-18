<?php

use Larakube\Service;

Service::create('laravel')
    ->setEnvironmentVariable('DB_HOST', 'K8_DB_HOST')
    ->setEnvironmentVariable('DB_USERNAME', 'K8_DB_USERNAME')
    ->setEnvironmentVariable('DB_PASSWORD', 'K8_DB_PASSWORD')
    ->enableService()
    ->setDockerfile('Dockerfile');

Service::create('database')
    ->setContainerPort(3306)
    ->setEnvironmentVariable('MYSQL_ROOT_PASSWORD')
    ->setEnvironmentVariable('MYSQL_DATABASE')
    ->enableService()
    ->setContainerImage('mysql', 'latest');
