<?php

use Larakube\Service;

Service::create('Laravel')
    ->setEnvironmentVariable('DB_HOST', 'K8_DB_HOST')
    ->setEnvironmentVariable('DB_USERNAME', 'K8_DB_USERNAME')
    ->setEnvironmentVariable('DB_PASSWORD', 'K8_DB_PASSWORD')
    ->setDockerfile('Dockerfile');

Service::create('database')
    ->setContainerPort(3306)
    ->setEnvironmentVariable('MYSQL_ROOT_PASSWORD')
    ->setEnvironmentVariable('MYSQL_DATABASE')
    ->setContainerImage('mysql', 'latest');

Service::create('cache')
    ->setContainerImage('redis', 'latest');
