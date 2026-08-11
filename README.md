# PHP knihovna pro komunikaci s Informačním systémem datových schránek (ISDS) Digitální a informační agentury

![DEV branch workflows](https://github.com/tomas-kulhanek/czech-data-box/actions/workflows/main.yml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/tomas-kulhanek/czech-data-box/v/stable)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![Total Downloads](https://poser.pugx.org/tomas-kulhanek/czech-data-box/downloads)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![Monthly Downloads](https://poser.pugx.org/tomas-kulhanek/czech-data-box/d/monthly)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![License](https://poser.pugx.org/tomas-kulhanek/czech-data-box/license)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)


⚠ **POZOR!!** Pokud implementujete napojení na ISDS, je potřeba aby jste se řídili dle [PROVOZNÍHO ŘÁDU](https://datovka.gov.cz/info/cs/80.html)⚠
## Instalace

### Composer

Pro instalaci balíčku je nutné jej instalovat skrze [composer](https://getcomposer.org/).

```bash
composer require tomas-kulhanek/czech-data-box
```

Dále je potřeba využít nějakého klienta. Buď je možné využít [Guzzle](https://github.com/guzzle/guzzle/) nebo [Symfony Http client](https://github.com/symfony/http-client)
```bash
composer require tomas-kulhanek/czech-data-box guzzlehttp/guzzle:^8.0
```
```bash
composer require tomas-kulhanek/czech-data-box symfony/http-client
```

V případě využívání vlastního http klienta, stačí implementovat rozhraní `TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface` a předat ho do konstruktoru třídy `TomasKulhanek\CzechDataBox\Connector`. Samozřejmostí je třeba zajistit správné nastavení hlaviček nebo SSL klientských certifikátů.

### Volitelná validace požadavků

Request DTO nesou atributy `#[Assert\*]` ze [symfony/validator](https://symfony.com/doc/current/validation.html), ale **knihovna sama validátor nespouští** — jsou to metadata pro vaši aplikaci. Pokud chcete požadavky validovat před odesláním, doinstalujte si validátor a zavolejte ho sami:

```bash
composer require symfony/validator
```

```php
use Symfony\Component\Validator\Validation;

$validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
$violations = $validator->validate($request);
if (count($violations) > 0) {
    // ošetřete chyby dříve, než požadavek předáte konektoru
}
```

Knihovna si sama hlídá jen kontroly, které vyplývají z Provozního řádu (limity velikosti a počtu příloh, povolené formáty, povinná pověření). Regresní shodu serializovaných požadavků se schématy Provozního řádu hlídá XSD validace v testech.

## Popis
Tato knihovna slouží k základní komunikaci s Informačním systémem datových schránek [ISDS](https://www.datovka.gov.cz) nebo [ISDS test](https://datovka-test.gov.cz)

## Základní použití
Pro každou operaci je potřebné zadat přístupové údaje

```php
<?php
$account = new \TomasKulhanek\CzechDataBox\Account();
$account->setPassword('mojeTajneHeslo')
        ->setLoginName('mujLogin')
        ->setLoginType(\TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum::NAME_PASSWORD);
```

Prostředí (produkce/test) určuje `EndpointProvider` předaný HTTP providerovi — výchozí je produkce:

```php
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;

$provider = GuzzleClientProvider::create();                            // produkce (datovka.gov.cz)
$provider = GuzzleClientProvider::create(EndpointProvider::test());    // test (datovka-test.gov.cz)
$provider = GuzzleClientProvider::create(new EndpointProvider('datovka.cms2.cz')); // vlastní doména (KIVS)
```

> [!WARNING]
> Vlastní doména musí pocházet z **důvěryhodné konfigurace, nikdy z uživatelského vstupu**. Na výslednou
> URL se posílají přihlašovací údaje (Basic Auth) i klientský certifikát, takže podvržená doména znamená
> jejich únik. `EndpointProvider` proto přijímá pouze holé jméno hostu — bez schématu, přihlašovacích
> údajů, portu a cesty (`datovka.cms2.cz` ano, `https://datovka.cms2.cz/` ne). Neplatná hodnota skončí
> výjimkou `TomasKulhanek\CzechDataBox\Exception\InvalidEndpointDomain`.

### Odeslání datové zprávy

`Connector::createMessage()` volá **hromadnou** operaci ISDS `CreateMultipleMessage` — i pro jedinou
zprávu. Z toho plynou dvě věci:

1. **Příjemci se zadávají mimo obálku**, přes `Recipient` (`dmRecipients`/`tRecipients`). Organizační
   jednotku příjemce nastavíte `Recipient::setOrgUnit()` / `setOrgUnitNum()`, „k rukám" pak
   `Recipient::setToHand()`. Obálka (`Envelope`, XSD typ `tMultipleMessageEnvelopeSub`) žádný prvek
   o příjemci nemá.
2. **Odpověď nemá přímé `dmID`.** Je typu `tMultipleMessageCreateOutput`, takže ID odeslané zprávy
   najdete až v dílčím stavu — `getMultipleStatus()` vrací pole `MessageStatus` (jeden na příjemce)
   a ID se čte z `MessageStatus::getDataMessageId()`.

```php
<?php

use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\Serializer\SerializerFactory;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

$connector = new Connector(SerializerFactory::create(), GuzzleClientProvider::create());

$recipient = new Recipient();
$recipient->setDataBoxId('abcdefg')
    ->setOrgUnit('Odbor právní')
    ->setToHand('Jan Novák');

$envelope = new Envelope();
$envelope->setAnnotation('Žádost o vyjádření')
    ->setSenderRefNumber('MUJ-2026/123');

$file = new File();
$file->setMimeType('application/pdf')
    ->setMetaType('main')
    ->setDescription('zadost.pdf')
    ->setEncodedContent(new SplFileInfo('/cesta/zadost.pdf'));

$request = new CreateMessage();
$request->setEnvelope($envelope)
    ->addRecipient($recipient)
    ->addFile($file);

$response = $connector->createMessage($account, $request);
if (!$response->isOk()) {
    throw new RuntimeException($response->getStatus()->getMessage());
}

foreach ($response->getMultipleStatus() as $messageStatus) {
    echo $messageStatus->getDataMessageId() . ': ' . $messageStatus->getStatus()->getMessage() . PHP_EOL;
}
```

### Načtení seznamu přijatých zpráv

Minimální příklad reálné operace — seznam přijatých zpráv s filtrem stavů a časovým rozsahem:

```php
<?php

use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfReceivedMessages;
use TomasKulhanek\CzechDataBox\Enum\FilterEnum;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\Serializer\SerializerFactory;

$connector = new Connector(SerializerFactory::create(), GuzzleClientProvider::create());

$request = new GetListOfReceivedMessages();
$request->setListFrom(new DateTimeImmutable('-7 days'))
    ->setListTo(new DateTimeImmutable())
    ->setStatusFilter(FilterEnum::DELIVERED, FilterEnum::READ)
    ->setLimit(50);

$response = $connector->getListOfReceivedMessages($account, $request);
if (!$response->getStatus()->isOk()) {
    throw new RuntimeException($response->getStatus()->getMessage());
}

foreach ($response->getRecord() as $record) {
    echo $record->getDataMessageId() . ': ' . $record->getAnnotation() . PHP_EOL;
}
```

## Využití s Symfony HTTP client
### Instalace
```bash
composer require tomas-kulhanek/czech-data-box symfony/http-client
```
#### Instancování
```php
$serializer = \TomasKulhanek\Serializer\SerializerFactory::create();
$guzzleProvider = \TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider::create();
$connector = new \TomasKulhanek\CzechDataBox\Connector($serializer, $guzzleProvider);
```

## Využití s Guzzle 8
### Instalace
```bash
composer require tomas-kulhanek/czech-data-box guzzlehttp/guzzle:^8.0
```
#### Instancování 
```php
$serializer = \TomasKulhanek\Serializer\SerializerFactory::create();
$guzzleProvider = \TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider::create();
$connector = new \TomasKulhanek\CzechDataBox\Connector($serializer, $guzzleProvider);
```
## Velkoobjemové datové zprávy (VoDZ)

Zprávy s přílohami nad 20 MB se odesílají jako velkoobjemové datové zprávy (VoDZ) s limitem **100 MB**.
Komunikace probíhá přes SOAP 1.2 na endpointech `ws2[c].…/DS/vodz` — knihovna to řeší automaticky.
Postup: každou přílohu nejprve nahrajte přes `uploadAttachment()`, poté odešlete zprávu přes
`createBigMessage()`, kde na nahrané přílohy odkážete pomocí `ExtFile` a vráceného identifikátoru.

```php
<?php

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;
use TomasKulhanek\CzechDataBox\DTO\BigMessageEnvelope;
use TomasKulhanek\CzechDataBox\DTO\BigMessageFiles;
use TomasKulhanek\CzechDataBox\DTO\ExtFile;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\UploadAttachment;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\Serializer\SerializerFactory;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

$account = new Account();
$account->setPassword('mojeTajneHeslo')
    ->setLoginName('mujLogin')
    ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

$connector = new Connector(SerializerFactory::create(), GuzzleClientProvider::create());

// 1) Nahrání přílohy (volá se zvlášť pro každou přílohu, součet max. 100 MB)
$attachment = new BigAttachment();
$attachment->setMimeType('application/pdf')
    ->setDescription('smlouva.pdf')
    ->setEncodedContent(new SplFileInfo('/cesta/ke/smlouva.pdf'));

$uploadRequest = new UploadAttachment();
$uploadRequest->setFile($attachment);

$uploadResponse = $connector->uploadAttachment($account, $uploadRequest);
if (!$uploadResponse->getStatus()->isOk()) {
    throw new RuntimeException($uploadResponse->getStatus()->getMessage());
}
$attachmentId = $uploadResponse->getAttachmentId(); // např. "ATT123456"

// 2) Odeslání zprávy odkazující na nahranou přílohu
$envelope = new BigMessageEnvelope();
$envelope->setType('V')
    ->setRecipientId('abcdefg')          // dbIDRecipient
    ->setAnnotation('Smlouva o dílo');   // dmAnnotation

$extFile = new ExtFile();
$extFile->setMetaType('main')            // hlavní příloha zprávy
    ->setAttachmentId($attachmentId)
    ->setAttachmentHash1($uploadResponse->getAttachmentHash1()->getValue())
    ->setAttachmentHash1Algorithm($uploadResponse->getAttachmentHash1()->getAlgorithm())
    ->setAttachmentHash2($uploadResponse->getAttachmentHash2()->getValue())
    ->setAttachmentHash2Algorithm($uploadResponse->getAttachmentHash2()->getAlgorithm());

$files = new BigMessageFiles();
$files->addExtFile($extFile);

$request = new CreateBigMessage();
$request->setEnvelope($envelope);
$request->setFiles($files);

$response = $connector->createBigMessage($account, $request);
if ($response->getStatus()->isOk()) {
    // zpráva byla úspěšně podána
}
```

Knihovna ještě před odesláním validuje vstupy a může vyhodit výjimky `MissingRequiredField`
(chybějící popis, obsah, příjemce či anotace), `MissingMainFile` (žádná příloha s `metaType` `main`),
`DisallowedAttachmentFormat` (přípona mimo whitelist vyhlášky č. 194/2009 Sb.),
`AttachmentCountOverflow` (příliš mnoho příloh), `FileSizeOverflow` (překročení 100 MB)
a `FieldLengthOverflow` (překročení délkových limitů obálky dle XSD — `dmAnnotation` 255 znaků,
`dmSenderRefNumber`/`dmRecipientRefNumber` a `dmSenderIdent`/`dmRecipientIdent` 50 znaků) —
všechny z namespace `TomasKulhanek\CzechDataBox\Exception`.

## Správa vlastní schránky (db_manipulations)

Operace služby `db_manipulations` (endpoint `…/DS/DsManage`) vyžadují oprávnění `PRIVIL_OWNER_ADM`.
Knihovna pokrývá správu pověřených osob — `getDataBoxUsers2()`, `addDataBoxUser2()`,
`updateDataBoxUser2()`, `deleteDataBoxUser2()` — a nově i tyto tři operace vlastníka schránky:

- `setOpenAddressing()` — zapne otevřené adresování (§ 18a), schránka pak může přijímat poštovní
  datové zprávy od kohokoli. Vstupem je jen `dbID` (a volitelně `dbApproved` / `dbExternRefNumber`).
- `clearOpenAddressing()` — otevřené adresování zase vypne, stejný vstup.
- `newAccessData2()` — vyžádá vydání nových přístupových údajů uživateli `isdsID` ve schránce `dbID`.
  Povinný je příznak `dbFeePaid` (zaplacený správní poplatek); pro virtuální obálku se navíc posílá
  `dbVirtual` a `email`, na nějž přijde odkaz na Aktivační portál. Odpověď vrací nové `dbUserID` a
  `dbAccessDataId` — samotné heslo webová služba nikdy nevrací, ISDS je doručuje mimo rozhraní.

```php
<?php

use TomasKulhanek\CzechDataBox\DTO\Request\NewAccessData2;
use TomasKulhanek\CzechDataBox\DTO\Request\SetOpenAddressing;

$response = $connector->setOpenAddressing($account, new SetOpenAddressing()->setDataBoxId('abcdefg'));
if (!$response->getStatus()->isOk()) {
    throw new RuntimeException($response->getStatus()->getMessage());
}

$request = new NewAccessData2();
$request->setDataBoxId('abcdefg')
    ->setIsdsId('a23456789012')
    ->setFeePaid(true);

$newAccessData = $connector->newAccessData2($account, $request);
echo $newAccessData->getAccessDataId();
```

Ostatní operace služby (`CreateDataBox2`, `DeleteDataBox2`, `EnableOwnDataBox2`, `DisableOwnDataBox2`,
`UpdateDataBoxDescr2`, `DisableDataBoxExternally2`) jsou určeny pro OVM/správce a knihovna je zatím
nepokrývá.
## Pokrytí webových služeb ISDS

<!-- wsdl-coverage:start -->
Matici generuje `php tools/wsdl-coverage.php` z WSDL v [`tests/_data/wsdl/`](tests/_data/wsdl)
(příloha 2 Provozního řádu, verze 3.11) a z reflexe třídy
`TomasKulhanek\CzechDataBox\Connector`. Soulad matice se skutečností hlídá CI
(`composer check:wsdl-coverage`), matici proto needitujte ručně.

Legenda: ✅ implementováno · ⛔ záměrně vynecháno (operaci nahradila novější varianta) ·
❌ neimplementováno (skutečná mezera).

### Souhrn

| WSDL | Operací | ✅ | ⛔ | ❌ |
| --- | ---: | ---: | ---: | ---: |
| [`db_access.wsdl`](#db_accesswsdl) | 6 | 6 | 0 | 0 |
| [`db_search.wsdl`](#db_searchwsdl) | 14 | 11 | 3 | 0 |
| [`db_manipulations.wsdl`](#db_manipulationswsdl) | 23 | 4 | 10 | 9 |
| [`dm_operations.wsdl`](#dm_operationswsdl) | 8 | 8 | 0 | 0 |
| [`dm_info.wsdl`](#dm_infowsdl) | 17 | 16 | 1 | 0 |
| [`dm_VoDZ.wsdl`](#dm_vodzwsdl) | 7 | 7 | 0 | 0 |
| [`dm_arch.wsdl`](#dm_archwsdl) | 1 | 1 | 0 | 0 |
| **Celkem** | **76** | **53** | **14** | **9** |

### `db_access.wsdl`

*služby související s přístupem do ISDS*

| Operace | Metoda `Connector` | Stav | Poznámka |
| --- | --- | --- | --- |
| `GetOwnerInfoFromLogin` | `getOwnerInfoFromLogin()` | ✅ | v knihovně označeno `#[Deprecated]` |
| `GetOwnerInfoFromLogin2` | `getOwnerInfoFromLogin2()` | ✅ | — |
| `GetUserInfoFromLogin` | `getUserInfoFromLogin()` | ✅ | v knihovně označeno `#[Deprecated]` |
| `GetUserInfoFromLogin2` | `getUserInfoFromLogin2()` | ✅ | — |
| `ChangeISDSPassword` | `changeIsdsPassword()` | ✅ | — |
| `GetPasswordInfo` | `getPasswordExpirationInfo()` | ✅ | — |

### `db_search.wsdl`

*vyhledávání datových schránek*

| Operace | Metoda `Connector` | Stav | Poznámka |
| --- | --- | --- | --- |
| `FindDataBox` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `FindDataBox2`; z API odstraněno v 6.0.0, viz [CHANGELOG.md](CHANGELOG.md) |
| `FindDataBox2` | `findDataBox2()` | ✅ | — |
| `CheckDataBox` | `checkDataBox()` | ✅ | — |
| `GetDataBoxList` | `getDataBoxList()` | ✅ | — |
| `PDZInfo` | `pdzInfo()` | ✅ | — |
| `DataBoxCreditInfo` | `dataBoxCreditInfo()` | ✅ | — |
| `ISDSSearch2` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `ISDSSearch3` |
| `ISDSSearch3` | `isdsSearch3()` | ✅ | — |
| `GetDataBoxActivityStatus` | `getDataBoxActivityStatus()` | ✅ | — |
| `FindPersonalDataBox` | — | ⛔ | zrušeno v ISDS 2018, nahrazeno `FindDataBox2`; z API odstraněno v 6.0.0, viz [CHANGELOG.md](CHANGELOG.md) |
| `DTInfo` | `dtInfo()` | ✅ | — |
| `PDZSendInfo` | `pdzSendInfo()` | ✅ | — |
| `GetConstants` | `getConstants()` | ✅ | — |
| `GetDataBoxAddress` | `getDataBoxAddress()` | ✅ | — |

### `db_manipulations.wsdl`

*manipulace s datovou schránkou a její uživatelé*

| Operace | Metoda `Connector` | Stav | Poznámka |
| --- | --- | --- | --- |
| `CreateDataBox` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `CreateDataBox2` |
| `CreateDataBox2` | — | ❌ | zřízení datové schránky (jen pro OVM s příslušnou rolí) |
| `DeleteDataBox` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `DeleteDataBox2` |
| `DeleteDataBox2` | — | ❌ | znepřístupnění datové schránky |
| `UpdateDataBoxDescr` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `UpdateDataBoxDescr2` |
| `UpdateDataBoxDescr2` | — | ❌ | změna popisných údajů schránky |
| `AddDataBoxUser` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `AddDataBoxUser2` |
| `AddDataBoxUser2` | `addDataBoxUser2()` | ✅ | — |
| `DeleteDataBoxUser` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `DeleteDataBoxUser2` |
| `DeleteDataBoxUser2` | `deleteDataBoxUser2()` | ✅ | — |
| `UpdateDataBoxUser` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `UpdateDataBoxUser2` |
| `UpdateDataBoxUser2` | `updateDataBoxUser2()` | ✅ | — |
| `NewAccessData` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `NewAccessData2` |
| `NewAccessData2` | — | ❌ | vygenerování nových přístupových údajů uživatele |
| `DisableDataBoxExternally` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `DisableDataBoxExternally2` |
| `DisableDataBoxExternally2` | — | ❌ | znepřístupnění cizí schránky (agenda OVM) |
| `DisableOwnDataBox` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `DisableOwnDataBox2` |
| `DisableOwnDataBox2` | — | ❌ | znepřístupnění vlastní schránky |
| `EnableOwnDataBox` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `EnableOwnDataBox2` |
| `EnableOwnDataBox2` | — | ❌ | zpřístupnění vlastní schránky |
| `SetOpenAddressing` | — | ❌ | zapnutí příjmu poštovních datových zpráv |
| `ClearOpenAddressing` | — | ❌ | vypnutí příjmu poštovních datových zpráv |
| `GetDataBoxUsers2` | `getDataBoxUsers2()` | ✅ | — |

### `dm_operations.wsdl`

*odesílání a stahování datových zpráv*

| Operace | Metoda `Connector` | Stav | Poznámka |
| --- | --- | --- | --- |
| `CreateMessage` | `createMessage()` | ✅ | knihovna posílá obálku `CreateMultipleMessage`, která pokrývá i jednoho příjemce |
| `MessageDownload` | `messageDownload()` | ✅ | — |
| `SignedMessageDownload` | `signedMessageDownload()` | ✅ | — |
| `SignedSentMessageDownload` | `signedSentMessageDownload()` | ✅ | — |
| `DummyOperation` | `dummyOperation()` | ✅ | — |
| `CreateMultipleMessage` | `createMessage()` | ✅ | hromadné odeslání (více příjemců v jednom volání) |
| `AuthenticateMessage` | `authenticateMessage()` | ✅ | — |
| `Re-signISDSDocument` | `resignIsdsDocument()` | ✅ | — |

### `dm_info.wsdl`

*informace o datových zprávách*

| Operace | Metoda `Connector` | Stav | Poznámka |
| --- | --- | --- | --- |
| `VerifyMessage` | `verifyMessage()` | ✅ | v knihovně označeno `#[Deprecated]` |
| `MessageEnvelopeDownload` | `messageEnvelopeDownload()` | ✅ | — |
| `MarkMessageAsDownloaded` | `markMessageAsDownloaded()` | ✅ | — |
| `GetDeliveryInfo` | `getDeliveryInfo()` | ✅ | — |
| `GetSignedDeliveryInfo` | `getSignedDeliveryInfo()` | ✅ | — |
| `GetListOfSentMessages` | `getListOfSentMessages()` | ✅ | — |
| `GetListOfReceivedMessages` | `getListOfReceivedMessages()` | ✅ | — |
| `GetMessageStateChanges` | `getMessageStateChanges()` | ✅ | — |
| `GetMessageAuthor` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `GetMessageAuthor2` |
| `GetMessageAuthor2` | `getMessageAuthor2()` | ✅ | — |
| `EraseMessage` | `eraseMessage()` | ✅ | — |
| `GetListOfErasedMessages` | `getListOfErasedMessages()` | ✅ | — |
| `PickUpAsyncResponse` | `pickUpAsyncResponse()` | ✅ | — |
| `GetListForNotifications` | `getListForNotifications()` | ✅ | — |
| `RegisterForNotifications` | `registerForNotifications()` | ✅ | — |
| `SentMessageEnvelopeDownload` | `sentMessageEnvelopeDownload()` | ✅ | — |
| `SuspMessageReport` | `suspMessageReport()` | ✅ | — |

### `dm_VoDZ.wsdl`

*velkoobjemové datové zprávy (VoDZ, do 100 MB)*

| Operace | Metoda `Connector` | Stav | Poznámka |
| --- | --- | --- | --- |
| `UploadAttachment` | `uploadAttachment()` | ✅ | — |
| `DownloadAttachment` | `downloadAttachment()` | ✅ | — |
| `CreateBigMessage` | `createBigMessage()` | ✅ | — |
| `AuthenticateBigMessage` | `authenticateBigMessage()` | ✅ | — |
| `SignedBigMessageDownload` | `signedBigMessageDownload()` | ✅ | — |
| `SignedSentBigMessageDownload` | `signedSentBigMessageDownload()` | ✅ | — |
| `BigMessageDownload` | `bigMessageDownload()` | ✅ | — |

### `dm_arch.wsdl`

*archivace (přerazítkování) ZFO*

| Operace | Metoda `Connector` | Stav | Poznámka |
| --- | --- | --- | --- |
| `ArchiveISDSDocument` | `archiveIsdsDocument()` | ✅ | — |

### Mimo záběr knihovny

Následující WSDL přílohy 2 knihovna vědomě neimplementuje, nejde tedy o mezery v pokrytí:

- **`ChangePassword.wsdl` (služba `asws`)** — změna hesla přes SMS kód / OTP (`SendSMSCode`, `ChangePasswordOTP`) běží na samostatné službě `asws` s vlastní autentizací. Knihovna podporuje běžnou změnu hesla operací `ChangeISDSPassword` z `db_access.wsdl`.
- **`SetConcept.wsdl`** — zakládání konceptů zpráv (`SetConcept`, `SetMultipleConcept`) je určené pro předání rozepsané zprávy do webového Portálu datových schránek, ne pro strojové odesílání. Knihovna zprávy odesílá přímo přes `dm_operations.wsdl`.
- **`ExtWs.wsdl` — odesílací brána (OB)** — odesílací brána (`extWsLogout`, `GetCredential`) je samostatný produkt ISDS s vlastním modelem autentizace a smluvním režimem. Knihovna cílí na přímé napojení aplikace na ISDS.
<!-- wsdl-coverage:end -->

## Povinnosti aplikace dle Provozního řádu ISDS

Knihovna řeší komunikaci s ISDS, ale některé povinnosti [Provozního řádu](https://datovka.gov.cz/info/cs/80.html) musí zajistit až vaše aplikace:

- **Evidujte již stažené zprávy a stahujte jen nové** (kap. II.17 „Dodržování přiměřenosti"). Aplikace nesmí opakovaně stahovat celé seznamy a obsahy zpráv — použijte filtry `GetListOfReceivedMessages`/`GetListOfSentMessages` (od–do, stavy) a vlastní evidenci zpracovaných `dmID`.
- **Lokální (desktopové) aplikace se smí přihlašovat pouze na manuální pokyn uživatele.** Serverové aplikace se mohou přihlašovat automatizovaně, ale jen v nezbytné frekvenci.
- **Počítejte s omezením počtu dotazů.** Při překračování denních limitů ISDS nejprve zasílá systémovou zprávu, poté odpovědi zdržuje o 3 sekundy a souběžný požadavek ze stejného účtu odmítá. Nespouštějte paralelní požadavky pod jedním účtem a implementujte přiměřený retry.
- **⚠ Přístupové údaje nesmí opustit zařízení pod plnou kontrolou uživatele.** Předání jména a hesla cloudové/webové aplikaci třetí strany je porušením § 9 odst. 2 zákona č. 300/2008 Sb. — Správce může takové údaje zneplatnit. Doporučená autentizace pro externí systémy je systémový certifikát (`LoginTypeEnum::SPIS_CERT`).
- **Doručení přihlášením** (§ 17 odst. 3) způsobuje výhradně volání `GetListOfReceivedMessages` — ostatní operace doručení nezpůsobí.
- **Údržba ISDS** probíhá zpravidla v pátek 0:00–1:00 (možná krátká nedostupnost); knihovna při HTTP 503 vyhazuje `SystemExclusion`.
- **Zprávy nad 20 MB** odesílejte jako velkoobjemové (VoDZ, do 100 MB) přes `uploadAttachment()` + `createBigMessage()`; hromadné odeslání u VoDZ není podporováno.
- Změny webových služeb oznamuje DIA zpravidla 2 měsíce předem na [stránce pro dodavatele](https://datovka.gov.cz/info/cs/74.html); dodavatelům aplikací se doporučuje [registrace do pracovního prostoru](https://registrace.poradnaisds.cz).

## Pomoc a řešení chyb

V případě že potřebujete poradit, nebo při implementaci Vám třída zobrazuje chybu vytvořte prosím nové Issues.
Základní pomoc je poskytována zcela zdarma pomocí Issues.

⚠ **Bezpečnostní chybu prosím nehlaste veřejným Issue** — postup najdete v [SECURITY.md](SECURITY.md).

## Odkazy
- Changelog knihovny - [CHANGELOG.md](CHANGELOG.md)
- Jak přispívat - [CONTRIBUTING.md](CONTRIBUTING.md)
- Bezpečnostní politika - [SECURITY.md](SECURITY.md)
- Produkční ISDS - https://www.datovka.gov.cz
- Testovací ISDS - https://datovka-test.gov.cz
- Provozní řád ISDS - https://datovka.gov.cz/info/cs/80.html
- Změny pro dodavatele aplikací - https://datovka.gov.cz/info/cs/74.html
- Poradna - https://poradnaisds.cz/

## Žádosti o zřízení datové schránky
### Produkční prostředí
- orgány veřejné moci - [odkaz](https://www.datoveschranky.info/documents/1744842/1746058/sprava_dalsich_DS_OVM.zfo/cfd889e3-0c11-4228-d87f-5c426dfc5ebb)
- ostatní - [odkaz](https://www.datoveschranky.info/documents/1744842/1746063/zadost_zrizeni_ds.zfo/42ee7c26-16dd-427f-94c8-319453efdae4)

### Testovací prostředí
Zřízení testovací schránky v prostředí datovka-test.gov.cz je možné skrze formulář na produkčním portálu www.datovka.gov.cz po přihlášení v nastavení
