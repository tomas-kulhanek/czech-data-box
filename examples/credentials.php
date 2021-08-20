<?php

use TomasKulhanek\CzechDataBox\Connector\DataBox;
use TomasKulhanek\CzechDataBox\Connector\DataMessage;
use TomasKulhanek\CzechDataBox\Connector\SearchDataBox;
use TomasKulhanek\Serializer\SerializerFactory;

/** @var \TomasKulhanek\CzechDataBox\Connector\Account $account */
if (file_exists('../../../autoload.php')) {
    require_once '../../../autoload.php';
} else {
    require_once '../vendor/autoload.php';
}
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
$console = new \Symfony\Component\Console\Output\ConsoleOutput();

$account = new \TomasKulhanek\CzechDataBox\Connector\Account();
$account->setProduction(false);
$account->setLoginType($account::LOGIN_NAME_PASSWORD);
$account->setLoginName('unhfjvx');
$account->setPassword('Admin123');

$client = \Symfony\Component\HttpClient\HttpClient::create();
$serializer = SerializerFactory::create();
$dataBox = new DataBox($serializer, $client);
$dataMessage = new DataMessage($serializer, $client);
$searchDataBox = new SearchDataBox($serializer, $client);
$manager = new \TomasKulhanek\CzechDataBox\Manager($dataBox, $dataMessage, $searchDataBox);
