# Czech Data Box — PHP knihovna pro datové schránky (ISDS)

Klient pro **Informační systém datových schránek (ISDS)** Digitální a informační agentury (DIA).
Odesílá i přijímá datové zprávy, zvládá velkoobjemové datové zprávy (VoDZ) a odpovídá aktuálnímu
rozhraní ISDS podle Provozního řádu účinného od 26. 06. 2026 (WSDL 3.11).

[![Run base actions](https://github.com/tomas-kulhanek/czech-data-box/actions/workflows/main.yml/badge.svg)](https://github.com/tomas-kulhanek/czech-data-box/actions/workflows/main.yml)
[![Latest Stable Version](https://poser.pugx.org/tomas-kulhanek/czech-data-box/v/stable)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![Total Downloads](https://poser.pugx.org/tomas-kulhanek/czech-data-box/downloads)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![Monthly Downloads](https://poser.pugx.org/tomas-kulhanek/czech-data-box/d/monthly)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![PHP verze](https://img.shields.io/packagist/dependency-v/tomas-kulhanek/czech-data-box/php)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![License](https://poser.pugx.org/tomas-kulhanek/czech-data-box/license)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)

```bash
composer require tomas-kulhanek/czech-data-box guzzlehttp/guzzle:^8.0
```

- ✅ **Odesílání i příjem datových zpráv** — `createMessage()`, `getListOfReceivedMessages()`, `messageDownload()`
- ✅ **Aktuální rozhraní ISDS** — WSDL verze 3.11 z přílohy 2 Provozního řádu účinného od 26. 06. 2026
- ✅ **[56 ze 76 operací](#pokrytí-webových-služeb-isds)** sedmi WSDL služeb ISDS; matici pokrytí generuje a v CI hlídá skript
- ✅ **Velkoobjemové datové zprávy (VoDZ)** do 100 MiB včetně uploadu příloh a stahování
- ✅ **Guzzle 8 i Symfony HttpClient 7/8** (kompatibilní se Symfony 8), nebo vlastní klient přes rozhraní
- ✅ **Produkční i testovací prostředí DIA** (`datovka.gov.cz` / `datovka-test.gov.cz`) i vlastní doména KIVS
- ✅ **PHP 8.4+**, typovaná DTO, PHPStan na levelu `max`
- ✅ **XSD validace serializovaných požadavků** proti schématům Provozního řádu + integrační testy proti testovacímu ISDS
- ✅ **Bezpečnostní hardening** — parsování odpovědí bez sítě (`LIBXML_NONET`), limit velikosti odpovědi, `#[\SensitiveParameter]` u hesel a klíčů

## Obsah

- [Instalace](#instalace)
- [Rychlý start](#rychlý-start)
- [Kompatibilita s ISDS](#kompatibilita-s-isds)
- [Odeslání datové zprávy](#odeslání-datové-zprávy)
- [Načtení seznamu přijatých zpráv](#načtení-seznamu-přijatých-zpráv)
- [Stažení obsahu zprávy](#stažení-obsahu-zprávy)
- [Volba HTTP klienta](#volba-http-klienta)
- [Velkoobjemové datové zprávy (VoDZ)](#velkoobjemové-datové-zprávy-vodz)
- [Správa vlastní schránky](#správa-vlastní-schránky-db_manipulations)
- [Pokrytí webových služeb ISDS](#pokrytí-webových-služeb-isds)
- [Povinnosti aplikace dle Provozního řádu ISDS](#povinnosti-aplikace-dle-provozního-řádu-isds)
- [Přechod z jiné ISDS knihovny](#přechod-z-jiné-isds-knihovny)

## Instalace

Balíček se instaluje přes [composer](https://getcomposer.org/) spolu s HTTP klientem —
[Guzzle](https://github.com/guzzle/guzzle/) nebo [Symfony HttpClient](https://github.com/symfony/http-client):

```bash
composer require tomas-kulhanek/czech-data-box guzzlehttp/guzzle:^8.0
```

```bash
composer require tomas-kulhanek/czech-data-box symfony/http-client
```

Knihovna vyžaduje PHP `^8.4` a rozšíření `curl`, `dom`, `mbstring`, `openssl` a `xml`.
Povyšujete-li z verze 5.x, řiďte se návodem [UPGRADE-6.0.md](UPGRADE-6.0.md); přecházíte-li
z jiné ISDS knihovny, viz [Přechod z jiné ISDS knihovny](#přechod-z-jiné-isds-knihovny).

Podrobnosti k oběma klientům jsou v sekci [Volba HTTP klienta](#volba-http-klienta).
V případě využívání vlastního http klienta, stačí implementovat rozhraní `TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface` a předat ho do konstruktoru třídy `TomasKulhanek\CzechDataBox\Connector`. Samozřejmostí je třeba zajistit správné nastavení hlaviček nebo SSL klientských certifikátů. Poslední parametr `sendRequest()` nese maximální velikost odpovědi v bajtech (`null` = výchozí limit implementace); uplatněte ho ještě před načtením celého těla do paměti — hotové počítadlo i kontrolu hlavičky `Content-Length` nabízí `TomasKulhanek\CzechDataBox\Provider\ResponseSizeLimit`.

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

## Rychlý start

Knihovna nemá stavové přihlášení — přístupové údaje nese `TomasKulhanek\CzechDataBox\Account`
a předávají se do každé operace. `Connector` je jediný vstupní bod ke všem operacím ISDS:

```php
<?php

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Serializer\SerializerFactory;

$account = new Account();
$account->setLoginName('mujLogin')
    ->setPassword('mojeTajneHeslo')
    ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

$connector = new Connector(SerializerFactory::create(), GuzzleClientProvider::create());

// nejjednodušší ověření přístupových údajů — informace o vlastníkovi schránky
$response = $connector->getOwnerInfoFromLogin2($account);
if (!$response->getStatus()->isOk()) {
    throw new RuntimeException($response->getStatus()->getMessage());
}

echo $response->getOwnerInfo()->getDataBoxId();
```

> [!IMPORTANT]
> Napojení na ISDS se řídí [Provozním řádem ISDS](https://datovka.gov.cz/info/cs/80.html). Část
> povinností (evidence stažených zpráv, frekvence dotazů, nakládání s přístupovými údaji) musí
> zajistit vaše aplikace — viz [Povinnosti aplikace dle Provozního řádu ISDS](#povinnosti-aplikace-dle-provozního-řádu-isds).

## Kompatibilita s ISDS

Řada **6.x** je sjednocená s Provozním řádem ISDS účinným od **26. 06. 2026** a s jeho přílohou 2,
tedy **WSDL verze 3.11**. Rozsah pokrytých operací najdete v
[matici pokrytí](#pokrytí-webových-služeb-isds), kterou generuje skript z WSDL v
[`tests/_data/wsdl/`](tests/_data/wsdl) a kontroluje CI.

Prostředí (produkce/test) určuje `EndpointProvider` předaný HTTP providerovi — výchozí je produkce:

| Prostředí | Doména | Zápis |
| --- | --- | --- |
| Produkce | `datovka.gov.cz` | `GuzzleClientProvider::create()` |
| Test | `datovka-test.gov.cz` | `GuzzleClientProvider::create(EndpointProvider::test())` |
| Vlastní (KIVS) | např. `datovka.cms2.cz` | `GuzzleClientProvider::create(new EndpointProvider('datovka.cms2.cz'))` |

```php
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;

$provider = GuzzleClientProvider::create();                            // produkce (datovka.gov.cz)
$provider = GuzzleClientProvider::create(EndpointProvider::test());    // test (datovka-test.gov.cz)
$provider = GuzzleClientProvider::create(new EndpointProvider('datovka.cms2.cz')); // vlastní doména (KIVS)
```

Původní domény `mojedatovaschranka.cz` a `czebox.cz` zůstávají podle DIA funkční minimálně do
31. 12. 2027, knihovna je ale od verze 6.0 už nepoužívá.

> [!WARNING]
> Vlastní doména musí pocházet z **důvěryhodné konfigurace, nikdy z uživatelského vstupu**. Na výslednou
> URL se posílají přihlašovací údaje (Basic Auth) i klientský certifikát, takže podvržená doména znamená
> jejich únik. `EndpointProvider` proto přijímá pouze holé jméno hostu — bez schématu, přihlašovacích
> údajů, portu a cesty (`datovka.cms2.cz` ano, `https://datovka.cms2.cz/` ne). Neplatná hodnota skončí
> výjimkou `TomasKulhanek\CzechDataBox\Exception\InvalidEndpointDomain`.

## Odeslání datové zprávy

Zprávu složíte ze tří částí: obálky (`Envelope`), jednoho či více příjemců (`Recipient`) a příloh
(`File`, právě jedna z nich musí mít `metaType` `main`). Odesílá `Connector::createMessage()`:

```php
<?php

use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Serializer\SerializerFactory;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

$connector = new Connector(SerializerFactory::create(), GuzzleClientProvider::create());
// $account vytvoříte podle sekce Rychlý start

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

Dvě věci, které při odesílání překvapí nejčastěji — obojí plyne z toho, že
`Connector::createMessage()` volá **hromadnou** operaci ISDS `CreateMultipleMessage` i pro jedinou
zprávu (metoda `createMultipleMessage()` proto v knihovně neexistuje):

1. **Příjemci se zadávají mimo obálku**, přes `Recipient` (`dmRecipients`/`tRecipients`). Organizační
   jednotku příjemce nastavíte `Recipient::setOrgUnit()` / `setOrgUnitNum()`, „k rukám" pak
   `Recipient::setToHand()`. Obálka (`Envelope`, XSD typ `tMultipleMessageEnvelopeSub`) žádný prvek
   o příjemci nemá.
2. **Odpověď nemá přímé `dmID`.** Je typu `tMultipleMessageCreateOutput`, takže ID odeslané zprávy
   najdete až v dílčím stavu — `getMultipleStatus()` vrací pole `MessageStatus` (jeden na příjemce)
   a ID se čte z `MessageStatus::getDataMessageId()`.

Před odesláním knihovna sama hlídá limity Provozního řádu: 1–50 příjemců, nejvýše 100 příloh
(z toho 10 kontejnerových), součet příloh do 20 MiB, povolené formáty dle vyhlášky č. 194/2009 Sb.
a délky polí obálky. Větší zprávy patří mezi [velkoobjemové (VoDZ)](#velkoobjemové-datové-zprávy-vodz).

## Načtení seznamu přijatých zpráv

Minimální příklad reálné operace — seznam přijatých zpráv s filtrem stavů a časovým rozsahem:

```php
<?php

use DateTimeImmutable;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfReceivedMessages;
use TomasKulhanek\CzechDataBox\Enum\FilterEnum;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Serializer\SerializerFactory;

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

Odeslané zprávy vrací obdobná operace `getListOfSentMessages()` s `GetListOfSentMessages`
(místo `setRecipientOrgUnitNum()` má `setSenderOrgUnitNum()`).

> [!WARNING]
> Volání `GetListOfReceivedMessages` je podle § 17 odst. 3 zákona č. 300/2008 Sb. **doručením
> přihlášením** — žádná jiná operace doručení nezpůsobí. Seznamy proto nestahujte v kratších
> intervalech, než odpovídá potřebě aplikace.

## Stažení obsahu zprávy

Seznam vrací jen obálky. Obsah zprávy včetně příloh stáhne `messageDownload()`; podepsanou variantu
(`dmSignature`, dostupná přes `getSignature()`) pak `signedMessageDownload()`. Operace
`markMessageAsDownloaded()` označí přijatou zprávu jako **přečtenou**:

```php
<?php

use TomasKulhanek\CzechDataBox\DTO\Request\MarkMessageAsDownloaded;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageDownload;

$markRequest = new MarkMessageAsDownloaded();
$markRequest->setDataMessageId('123456789');
$connector->markMessageAsDownloaded($account, $markRequest);

$downloadRequest = new MessageDownload();
$downloadRequest->setDataMessageId('123456789');

$downloaded = $connector->messageDownload($account, $downloadRequest);
$envelope = $downloaded->getReturnedMessage()->getDataMessage();

foreach ($envelope->getFiles() as $file) {
    echo $file->getDescription() . ' (' . $file->getMetaType() . ')' . PHP_EOL;

    $content = $file->getEncodedContent()?->getContents();
    if ($content !== null) {
        file_put_contents('/cesta/' . $file->getDescription(), $content);
    }
}
```

## Volba HTTP klienta

Knihovna žádného HTTP klienta nevyžaduje — komunikaci obstarává implementace
`TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface`. Přibalené jsou dvě:

```bash
composer require tomas-kulhanek/czech-data-box guzzlehttp/guzzle:^8.0
```

```php
$serializer = \TomasKulhanek\CzechDataBox\Serializer\SerializerFactory::create();
$provider = \TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider::create();
$connector = new \TomasKulhanek\CzechDataBox\Connector($serializer, $provider);
```

```bash
composer require tomas-kulhanek/czech-data-box symfony/http-client
```

```php
$serializer = \TomasKulhanek\CzechDataBox\Serializer\SerializerFactory::create();
$provider = \TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider::create();
$connector = new \TomasKulhanek\CzechDataBox\Connector($serializer, $provider);
```

Podporované rozsahy jsou `guzzlehttp/guzzle ^8.0` a `symfony/http-client 7.*|8.*` — knihovna je tedy
použitelná i v aplikacích na Symfony 8.

## Velkoobjemové datové zprávy (VoDZ)

Zprávy s přílohami nad 20 MiB se odesílají jako velkoobjemové datové zprávy (VoDZ) s limitem
**100 MiB** (`Connector::MAX_BIG_MESSAGE_ATTACHMENTS_SIZE`; Provozní řád mluví o 100 MB, knihovna
limit počítá binárně ve prospěch odesílatele). Komunikace probíhá přes SOAP 1.2 na endpointech
`ws2[c].…/DS/vodz` — knihovna to řeší automaticky.

Oproti běžné zprávě je postup dvoufázový a příjemce je právě jeden (hromadné odeslání ISDS u VoDZ
nepodporuje): každou přílohu nejprve nahrajte přes `uploadAttachment()`, poté odešlete zprávu přes
`createBigMessage()`, kde na nahrané přílohy odkážete pomocí `ExtFile` a vráceného identifikátoru.
Stahování obstarávají `bigMessageDownload()`, `signedBigMessageDownload()`,
`signedSentBigMessageDownload()`, `downloadAttachment()` a ověření `authenticateBigMessage()`.

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
use TomasKulhanek\CzechDataBox\Serializer\SerializerFactory;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

$account = new Account();
$account->setPassword('mojeTajneHeslo')
    ->setLoginName('mujLogin')
    ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

$connector = new Connector(SerializerFactory::create(), GuzzleClientProvider::create());

// 1) Nahrání přílohy (volá se zvlášť pro každou přílohu, součet max. 100 MiB)
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
`AttachmentCountOverflow` (příliš mnoho příloh), `FileSizeOverflow` (překročení 100 MiB)
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
Tabulka ukazuje, které operace rozhraní ISDS knihovna umí — pro každou operaci z WSDL uvádí
odpovídající metodu `Connector`, nebo důvod, proč pokrytá není. Slouží jako kontrola před
nasazením: než začnete integraci psát, ověříte si v ní, že operace, kterou potřebujete, existuje.

Matici generuje `php tools/wsdl-coverage.php` z WSDL v [`tests/_data/wsdl/`](tests/_data/wsdl)
(příloha 2 Provozního řádu, verze 3.11) a z reflexe třídy
`TomasKulhanek\CzechDataBox\Connector` — čísla proto nemohou zastarat vůči kódu. Soulad matice
se skutečností hlídá CI (`composer check:wsdl-coverage`), matici proto needitujte ručně.

Legenda: ✅ implementováno · ⛔ záměrně vynecháno (operaci nahradila novější varianta) ·
❌ neimplementováno (skutečná mezera).

### Souhrn

| WSDL | Operací | ✅ | ⛔ | ❌ |
| --- | ---: | ---: | ---: | ---: |
| [`db_access.wsdl`](#db_accesswsdl) | 6 | 6 | 0 | 0 |
| [`db_search.wsdl`](#db_searchwsdl) | 14 | 11 | 3 | 0 |
| [`db_manipulations.wsdl`](#db_manipulationswsdl) | 23 | 7 | 10 | 6 |
| [`dm_operations.wsdl`](#dm_operationswsdl) | 8 | 8 | 0 | 0 |
| [`dm_info.wsdl`](#dm_infowsdl) | 17 | 16 | 1 | 0 |
| [`dm_VoDZ.wsdl`](#dm_vodzwsdl) | 7 | 7 | 0 | 0 |
| [`dm_arch.wsdl`](#dm_archwsdl) | 1 | 1 | 0 | 0 |
| **Celkem** | **76** | **56** | **14** | **6** |

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
| `NewAccessData2` | `newAccessData2()` | ✅ | — |
| `DisableDataBoxExternally` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `DisableDataBoxExternally2` |
| `DisableDataBoxExternally2` | — | ❌ | znepřístupnění cizí schránky (agenda OVM) |
| `DisableOwnDataBox` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `DisableOwnDataBox2` |
| `DisableOwnDataBox2` | — | ❌ | znepřístupnění vlastní schránky |
| `EnableOwnDataBox` | — | ⛔ | starší varianta, ISDS ji nahradilo operací `EnableOwnDataBox2` |
| `EnableOwnDataBox2` | — | ❌ | zpřístupnění vlastní schránky |
| `SetOpenAddressing` | `setOpenAddressing()` | ✅ | — |
| `ClearOpenAddressing` | `clearOpenAddressing()` | ✅ | — |
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

*velkoobjemové datové zprávy (VoDZ, do 100 MiB)*

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
- **Zprávy nad 20 MiB** odesílejte jako velkoobjemové (VoDZ, do 100 MiB) přes `uploadAttachment()` + `createBigMessage()`; hromadné odeslání u VoDZ není podporováno.
- Změny webových služeb oznamuje DIA zpravidla 2 měsíce předem na [stránce pro dodavatele](https://datovka.gov.cz/info/cs/74.html); dodavatelům aplikací se doporučuje [registrace do pracovního prostoru](https://registrace.poradnaisds.cz).

## Přechod z jiné ISDS knihovny

Migrujete-li existující integraci z balíčku `dfridrich/czech-data-box`, projděte si
[Přechod z dfridrich/czech-data-box](MIGRATION-FROM-DFRIDRICH.md) — obsahuje mapu API obou
knihoven, dva úplné příklady „před → po“ (přihlášení se seznamem přijatých zpráv a odeslání
zprávy) a checklist migrace.

Povyšujete-li z verze 5.x této knihovny, řiďte se návodem [UPGRADE-6.0.md](UPGRADE-6.0.md).

## Pomoc a řešení chyb

V případě že potřebujete poradit, nebo při implementaci Vám třída zobrazuje chybu vytvořte prosím nové Issues.
Základní pomoc je poskytována zcela zdarma pomocí Issues.

⚠ **Bezpečnostní chybu prosím nehlaste veřejným Issue** — postup najdete v [SECURITY.md](SECURITY.md).

## Odkazy
- Changelog knihovny - [CHANGELOG.md](CHANGELOG.md)
- Migrace z 5.x na 6.0 - [UPGRADE-6.0.md](UPGRADE-6.0.md)
- Přechod z `dfridrich/czech-data-box` - [MIGRATION-FROM-DFRIDRICH.md](MIGRATION-FROM-DFRIDRICH.md)
- Jak přispívat - [CONTRIBUTING.md](CONTRIBUTING.md)
- Bezpečnostní politika - [SECURITY.md](SECURITY.md)
- Produkční ISDS - https://www.datovka.gov.cz
- Testovací ISDS - https://datovka-test.gov.cz
- Provozní řád ISDS - https://datovka.gov.cz/info/cs/80.html
- Změny pro dodavatele aplikací - https://datovka.gov.cz/info/cs/74.html
- Poradna - https://poradnaisds.cz/

## Žádosti o zřízení datové schránky
### Produkční prostředí
Formuláře žádostí pro orgány veřejné moci i ostatní typy schránek vydává DIA na stránce
[Zřízení datové schránky](https://datovka.gov.cz/info/cs/66.html). Původní odkazy na
`datoveschranky.info` už nefungují, portál se přesunul na `datovka.gov.cz`.

### Testovací prostředí
Zřízení testovací schránky v prostředí datovka-test.gov.cz je možné skrze formulář na produkčním portálu www.datovka.gov.cz po přihlášení v nastavení

---

Používáte knihovnu v produkci? Budu rád za ⭐ na GitHubu — pomáhá ostatním vývojářům najít
aktuálně udržovanou implementaci ISDS.
