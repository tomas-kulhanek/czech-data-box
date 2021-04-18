<?php

use TomasKulhanek\CzechDataBox\Connector\DataBox;
use TomasKulhanek\CzechDataBox\Connector\DataMessage;
use TomasKulhanek\CzechDataBox\Connector\SearchDataBox;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\PortalTypeEnum;
use TomasKulhanek\Serializer\SerializerFactory;

if(file_exists('../../../autoload.php')){
    require_once '../../../autoload.php';
}else{
    require_once '../vendor/autoload.php';
}
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
$console = new \Symfony\Component\Console\Output\ConsoleOutput();

$account = new \TomasKulhanek\CzechDataBox\Connector\Account();
$account->setPortalType(\TomasKulhanek\CzechDataBox\Enum\PortalTypeEnum::get(\TomasKulhanek\CzechDataBox\Enum\PortalTypeEnum::CZEBOX));
$account->setLoginType(\TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum::get(\TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum::LOGIN_HOSTED_SPIS));
$account->setCertPrivateFileName(realpath(__DIR__ . '/../../../temp/systemovy.key.pem'));
$account->setCertPublicFileName(realpath(__DIR__ . '/../../../temp/systemovy.crt.pem'));
$account->setDataBoxId('unhfjvx');
$account->setPassPhrase('Admin123');

$client = \Symfony\Component\HttpClient\HttpClient::create();
$serializer = SerializerFactory::create();
$dataBox = new DataBox($serializer, $client);
$dataMessage = new DataMessage($serializer, $client);
$searchDataBox = new SearchDataBox($serializer, $client);
$manager = new \TomasKulhanek\CzechDataBox\Manager($dataBox, $dataMessage, $searchDataBox);
