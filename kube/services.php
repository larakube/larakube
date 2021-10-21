<?php

use Larakube\Cluster\Deployment;
use Larakube\Cluster\EnvironmentVariable;
use Larakube\Cluster\Service;

/*
|--------------------------------------------------------------------------
| Laravel Service
|--------------------------------------------------------------------------
*/
Deployment::create('laravel')
    ->setContainerPort(80)
    ->setReplicaCount(2)
    ->setDockerFilePath('Dockerfile')
    ->setEnvironmentVariable(new EnvironmentVariable('DB_HOST', 'mysql'))
    ->setEnvironmentVariable(new EnvironmentVariable('DB_USERNAME', 'root', fromEnvironmentVariableName: 'DB_USERNAME'))
    ->setEnvironmentVariable(new EnvironmentVariable('DB_PASSWORD', fromEnvironmentVariableName: 'DB_PASSWORD'));

Service::create('laravel')
    ->setTargetPort(80)
    ->setServicePort(80)
    ->loadBalancer();

/*
|--------------------------------------------------------------------------
| MySQL Service
|--------------------------------------------------------------------------
*/
Deployment::create('mysql')
    ->setContainerPort(3306)
    ->setReplicaCount(1)
    ->setContainerImage('mysql')
    ->setEnvironmentVariable(new EnvironmentVariable('MYSQL_ROOT_PASSWORD', fromEnvironmentVariableName: 'DB_PASSWORD'))
    ->setEnvironmentVariable(new EnvironmentVariable('MYSQL_DATABASE', fromEnvironmentVariableName: 'DB_DATABASE'))
    ->setEnvironmentVariable(new EnvironmentVariable('MYSQL_USER', fromEnvironmentVariableName: 'DB_USERNAME'))
    ->setEnvironmentVariable(new EnvironmentVariable('MYSQL_PASSWORD', fromEnvironmentVariableName: 'DB_PASSWORD'));

Service::create('mysql')
    ->setTargetPort(3306)
    ->setServicePort(3306);
