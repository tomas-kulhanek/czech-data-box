<?php
require 'credentials.php';
$console->writeln(sprintf('Komunikovat bude probihat vuci %s za datovou schranku s id %s', ($account->getPortalType()->equalsValue(\TomasKulhanek\CzechDataBox\Enum\PortalTypeEnum::MOJEDATOVASCHRANKA) ? 'mojedatovaschranka.cz' : 'czebox.cz'), $account->getDataBoxId()));
/** @var \TomasKulhanek\CzechDataBox\Manager $manager */

$ownerInfo = new \TomasKulhanek\CzechDataBox\Entity\OwnerInfo();
$ownerInfo->setDataBoxId($account->getDataBoxId());
/***********************************/
$console->write('   - Probiha fulltextove vyhledavani');
$input = new \TomasKulhanek\CzechDataBox\Request\ISDSSearch3();
$input->setSearchText('Ministerstvo')
    ->setSearchType(\TomasKulhanek\CzechDataBox\Utils\DataBoxStatus::TYPE_GENERAL)
    ->setSearchScope(\TomasKulhanek\CzechDataBox\Utils\DataBoxStatus::SCOPE_OVM);
/** @var \TomasKulhanek\CzechDataBox\Response\ISDSSearch3 */
$res = $manager->isdsSearch3($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK ' . sprintf('Bylo nalezeno celkem %d zaznamu a nacteno je %d', $res->getTotalCount(), $res->getCurrentCount()));
    /** @var \TomasKulhanek\CzechDataBox\Entity\DataBoxResult $result */
    foreach ($res->getResult() as $key => $result) {
        $console->writeln(sprintf('     %d. - %s => %s', $key, $result->getDataBoxId(), $result->getDataBoxName()));
        if ($res->getResult()->indexOf($result) < 1) {
            $console->writeln('         Zjistovani zda je schranka aktivni');
            $ch = new \TomasKulhanek\CzechDataBox\Request\CheckDataBox();
            $ch->setDataBoxId($result->getDataBoxId());
            /** @var \TomasKulhanek\CzechDataBox\Response\CheckDataBox $resT */
            $resT = $manager->checkDataBox($account, $ch);
            if ($res->getStatus()->isOk()) {
                $console->writeln('         -> schranka ' . ($resT->getState() == 1 ? 'je' : 'neni') . ' aktivni');
            } else {
                $console->writeln('         -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
            }
        }
    }
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
/*********************************************************/
$console->write('   - hledani dle FindDataBox');
$input = new \TomasKulhanek\CzechDataBox\Request\FindDataBox();
$input->setOwnerInfo($ownerInfo);
/** @var \TomasKulhanek\CzechDataBox\Response\FindDataBox $res */
$res = $manager->findDataBox($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK - bylo nalezeno celkem ' . $res->getResult()->count());
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
/*********************************************************/
$console->write('   - zjistovani informaci o PDZ');
/** @var \TomasKulhanek\CzechDataBox\Response\VerifyMessage $res */
$input = new \TomasKulhanek\CzechDataBox\Request\PDZInfo();
$input->setSender($account->getDataBoxId());
$res = $manager->pdzInfo($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK');
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
/*********************************************************/
$console->write('   - zjistovani informaci o stavu creditu');
/** @var \TomasKulhanek\CzechDataBox\Response\DataBoxCreditInfo $res */
$input = new \TomasKulhanek\CzechDataBox\Request\DataBoxCreditInfo();
$input->setDataBoxId($account->getDataBoxId())
    ->setFromDate((new \DateTimeImmutable())->modify('-3 month'))
    ->setToDate((new DateTimeImmutable()));
$res = $manager->dataBoxCreditInfo($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK ' . sprintf('aktualni stav je %s', $res->getCurrentCredit()));
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
/*********************************************************/
$console->write('   - GetDataBoxActivityStatus');
$input = new \TomasKulhanek\CzechDataBox\Request\GetDataBoxActivityStatus();
$input->setDataBoxId($account->getDataBoxId())
    ->setFrom((new \DateTimeImmutable())->modify('-3 month'))
    ->setTo((new DateTimeImmutable()));
/** @var \TomasKulhanek\CzechDataBox\Response\GetDataBoxActivityStatus $res */
$res = $manager->getDataBoxActivityStatus($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK');
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
/*********************************************************/
$console->write('   - zjistovani informaci o datovem trezoru');
$input = new \TomasKulhanek\CzechDataBox\Request\DTInfo();
$input->setDataBoxId($account->getDataBoxId());
/** @var \TomasKulhanek\CzechDataBox\Response\DTInfo $res */
$res = $manager->dtInfo($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK');
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
/*********************************************************/
$console->write('   - zjistovani, zda muze subjekt odesilat postovni datove zpravy');
$input = new \TomasKulhanek\CzechDataBox\Request\PDZSendInfo();
$input->setDataBoxId($account->getDataBoxId());
/** @var \TomasKulhanek\CzechDataBox\Response\PDZSendInfo $res */
$res = $manager->pdzSendInfo($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK - subjekt ' . ($res->isResult() ? 'muze' : 'nemuze') . ' odesilat PDZ');
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
/*********************************************************/
$console->write('   - vyhledavani osobnich DS pro OVM');
$input = new \TomasKulhanek\CzechDataBox\Request\FindPersonalDataBox();
$input->setOwnerInfo((new \TomasKulhanek\CzechDataBox\Entity\PersonalOwnerInfo()));
$input->getOwnerInfo()->setDataBoxId($account->getDataBoxId());
/** @var \TomasKulhanek\CzechDataBox\Response\FindPersonalDataBox $res */
$res = $manager->findPersonalDataBox($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK  nalezeno celkem ' . $res->getRecord()->count() . ' subjektu');
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
/*********************************************************/
$console->write('   - GetDataBoxList');
$input = new \TomasKulhanek\CzechDataBox\Request\GetDataBoxList();
$input->setType(\TomasKulhanek\CzechDataBox\Utils\DataBoxStatus::ALL);
/** @var \TomasKulhanek\CzechDataBox\Response\GetDataBoxList $res */
$res = $manager->getDataBoxList($account, $input);
if ($res->getStatus()->isOk()) {
    $console->writeln(' -> OK');
} else {
    $console->writeln(' -> FAIL [' . $res->getStatus()->getCode() . '] ' . $res->getStatus()->getMessage());
}
