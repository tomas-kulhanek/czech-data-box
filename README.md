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

## Odkazy
- Changelog knihovny - [CHANGELOG.md](CHANGELOG.md)
- Jak přispívat - [CONTRIBUTING.md](CONTRIBUTING.md)
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
