<?php

const LARAKUBE_ROOT = __DIR__;

function package_root(string $path): string
{
    return sprintf(
        '%s%s%s',
        LARAKUBE_ROOT,
        DIRECTORY_SEPARATOR,
        ltrim($path, DIRECTORY_SEPARATOR)
    );
}