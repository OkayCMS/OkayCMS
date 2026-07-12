<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Okay',
        __DIR__ . '/backend',
    ])
    ->withSkip([
        __DIR__ . '/Okay/Modules/OkayCMS/AutoDeploy/bin',
        __DIR__ . '/*/vendor',
        __DIR__ . '/*/compiled',
    ])
    // PHP level sets follow composer.json (currently ^8.5)
    ->withPhpSets()
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
        TypedPropertyFromStrictConstructorRector::class,
    ])
    // Uncomment during specific stages:
    // ->withSets([
    //     SetList::DEAD_CODE,
    //     SetList::CODE_QUALITY,
    //     SetList::TYPE_DECLARATION,
    // ])
    ->withImportNames(
        importNames: true,
        importDocBlockNames: true,
        importShortClasses: false,
        removeUnusedImports: true,
    );
