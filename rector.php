<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withSkip([
        // The package renames the keys of whatever array it is handed, and a key that
        // looks like a number is an integer to PHP and a string to the caller who wrote
        // it down. Strict types would make that difference a TypeError.
        SafeDeclareStrictTypesRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withPhpSets();
