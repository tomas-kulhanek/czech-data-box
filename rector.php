<?php

declare(strict_types=1);

use Rector\Set\ValueObject\LevelSetList;

return static function (\Rector\Config\RectorConfig $containerConfigurator): void {
	$containerConfigurator->paths([
		__DIR__ . '/src'
	]);

	$containerConfigurator->import(LevelSetList::UP_TO_PHP_80);

	$containerConfigurator->phpstanConfig( __DIR__ . '/phpstan.neon');
    $containerConfigurator->skip([
        \Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class,
    ]);
    $containerConfigurator->importNames();
    $containerConfigurator->importShortClasses(false);
};
