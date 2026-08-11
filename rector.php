<?php

declare(strict_types=1);


return \Rector\Config\RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ])
    ->withPhpSets(php84: true)
    ->withSkip([
        \Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class
    ])
    ->withImportNames();
