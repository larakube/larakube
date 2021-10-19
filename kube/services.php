<?php

use Larakube\Service;

Service::create('laravel')
    ->setContainerPort(80)
    ->enableService()
    ->setEnvironmentVariable('DB_HOST', 'database', 'DB_HOST')
    ->setEnvironmentVariable('DB_USERNAME', 'root', 'DB_USERNAME')
    ->setEnvironmentVariable('DB_PASSWORD', '', 'DB_PASSWORD')
    ->setDockerfile('Dockerfile');

Service::create('database')
    ->setContainerPort(3306)
    ->enableService()
    ->setEnvironmentVariable('MYSQL_ROOT_PASSWORD', '', 'DB_PASSWORD')
    ->setEnvironmentVariable('MYSQL_DATABASE', '', 'DB_DATABASE')
    ->setContainerImage('mysql', 'latest');
