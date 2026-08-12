<?php

declare(strict_types=1);

use TomasKulhanek\CzechDataBox\Tools\MarkdownLinks;

require_once __DIR__ . '/../vendor/autoload.php';

$links = new MarkdownLinks(dirname(__DIR__));
$mode = $argv[1] ?? '--local';

switch ($mode) {
    case '--local':
        $problems = $links->checkLocal();
        $label = 'Odkazy na soubory a kotvy';
        break;

    case '--external':
        $problems = $links->checkExternal();
        $label = 'Externí odkazy';
        break;

    case '--all':
        $problems = array_merge($links->checkLocal(), $links->checkExternal());
        $label = 'Odkazy v dokumentaci';
        break;

    default:
        fwrite(STDERR, sprintf('Neznámý přepínač "%s". Použijte --local, --external nebo --all.%s', $mode, PHP_EOL));
        exit(2);
}

if ($problems === []) {
    echo $label . ' v Markdown dokumentaci jsou v pořádku.' . PHP_EOL;
    exit(0);
}

fwrite(STDERR, $label . ' v Markdown dokumentaci mají problém:' . PHP_EOL);
fwrite(STDERR, implode(PHP_EOL, $problems) . PHP_EOL);
exit(1);
