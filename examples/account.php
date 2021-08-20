<?php
/** @var \TomasKulhanek\CzechDataBox\Connector\Account $account */
require 'credentials.php';
$console->writeln(sprintf('Komunikovat bude probihat vuci %s za datovou schranku s id %s ', ($account->isProduction() ? 'mojedatovaschranka.cz' : 'czebox.cz'), $account->getDataBoxId()));
/** @var \TomasKulhanek\CzechDataBox\Manager $manager */

/***********************************/
$console->write('   - Probiha ziskavani informaci dle loginu');
$res = $manager->getOwnerInfoFromLogin($account);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK ' . sprintf('DS "%s" typ subjektu "%s"', $res->getOwnerInfo()->getFirmName(), $res->getOwnerInfo()->getDataBoxType()));
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}

/***********************************/
$console->write('   - Probiha ziskavani informaci o expiraci hesla');
$res = $manager->getPasswordExpirationInfo($account);
if ($res->getStatus()->isOk()) {
    $console->write(' -> OK ');
    if ($res->getPasswordExpiry() === null) {
        $console->writeln('heslo nema nastavenou expiraci a je platne bez omezeni');
    } else {
        $console->writeln(sprintf('heslo expiruje %s', $res->getPasswordExpiry()->format('j.n.Y, G:i')));
    }
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
