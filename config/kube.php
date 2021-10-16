<?php

return [
    'services' => [
        /*
        |--------------------------------------------------------------------------
        | Services Path
        |--------------------------------------------------------------------------
        |
        | The path to the directory where your services are kept.
        */
        'path' => 'kube/services',
    ],

    'registry' => [
        /*
        |--------------------------------------------------------------------------
        | Registry Base URL
        |--------------------------------------------------------------------------
        |
        | The base URL of the container/docker registry to upload images to, you do
        | not need to specify the image name and tag.
        |
        | Google Cloud:
        |   Format: HOSTNAME/PROJECT-ID
        |   Example: eu.gcr.io/my-project
        |
        | Amazon Web Services:
        |   Format: AWS_ACCOUNT_ID.dkr.ecr.REGION.amazonaws.com/MY_REPOSITORY
        |   Example: 12345678.dkr.ecr.eu-west-2.amazonaws.com/laravel
        |
        | Digital Ocean:
        |   Format: registry.digitalocean.com/<my-registry>
        |   Example: registry.digitalocean.com/laravel
        |
        */
        'base_url' => env('LARAKUBE_CONTAINER_REGISTRY_BASE_URL', null),
    ],
];
