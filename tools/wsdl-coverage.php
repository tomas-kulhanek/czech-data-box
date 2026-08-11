<?php

declare(strict_types=1);

use TomasKulhanek\CzechDataBox\Tools\WsdlCoverage;

require_once __DIR__ . '/../vendor/autoload.php';

$root = dirname(__DIR__);
$coverage = new WsdlCoverage($root . '/tests/_data/wsdl', $root . '/README.md');
$mode = $argv[1] ?? '--print';

switch ($mode) {
    case '--print':
        echo $coverage->render();
        exit(0);

    case '--check':
        $differences = $coverage->check();
        if ($differences === []) {
            echo 'Matice pokrytí WSDL v README.md odpovídá kódu.' . PHP_EOL;
            exit(0);
        }
        fwrite(STDERR, 'Matice pokrytí WSDL v README.md neodpovídá kódu:' . PHP_EOL);
        fwrite(STDERR, implode(PHP_EOL, $differences) . PHP_EOL);
        fwrite(STDERR, 'Spusťte `php tools/wsdl-coverage.php --write`.' . PHP_EOL);
        exit(1);

    case '--write':
        echo $coverage->write()
            ? 'Matice pokrytí WSDL v README.md byla aktualizována.' . PHP_EOL
            : 'Matice pokrytí WSDL v README.md je aktuální.' . PHP_EOL;
        exit(0);

    default:
        fwrite(STDERR, sprintf('Neznámý přepínač "%s". Použijte --print, --check nebo --write.%s', $mode, PHP_EOL));
        exit(2);
}
