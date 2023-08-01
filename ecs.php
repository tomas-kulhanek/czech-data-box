<?php

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $containerConfigurator): void {
    $containerConfigurator->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ]);
    $containerConfigurator->fileExtensions(['php', 'phpt']);

    $containerConfigurator->sets([SetList::PSR_12]);
    $containerConfigurator->rule(\PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class);
    $containerConfigurator->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
    $containerConfigurator->indentation('tab');
    $containerConfigurator->lineEnding("\n");
};