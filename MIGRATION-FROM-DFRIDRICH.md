# Přechod z `dfridrich/czech-data-box`

Tato příručka pomáhá převést existující integraci ISDS z balíčku
[`dfridrich/czech-data-box`](https://github.com/dfridrich/CzechDataBox) na API
`tomas-kulhanek/czech-data-box` řady **6.x**.

Obě knihovny umožňují datové zprávy odesílat i přijímat, liší se ale architekturou a rozsahem
podporovaného rozhraní ISDS:

| | `dfridrich/czech-data-box` | `tomas-kulhanek/czech-data-box` 6.x |
| --- | --- | --- |
| PHP | `^7.2\|^8.1` | `^8.4` |
| HTTP vrstva | `ext-soap` (`SoapClient`) | vlastní vrstva nad Guzzle 8 / Symfony HttpClient 7–8 |
| Datové struktury | třídy generované z WSDL (`Api\t*`) | ručně psaná typovaná DTO se serializací přes `jms/serializer` |
| Přístupové body služeb | `DataBoxAccess`, `DataBoxSearch`, `DmInfoWebService`, `DmOperationsWebService`, `IsdsStat` | jediná třída `Connector` nad sedmi WSDL (`db_access`, `db_search`, `db_manipulations`, `dm_operations`, `dm_info`, `dm_VoDZ`, `dm_arch`) |
| Rozsah pokrytí | — | [56 ze 76 operací](README.md#pokrytí-webových-služeb-isds), matici generuje a v CI kontroluje skript |

Migraci má smysl zvažovat hlavně tehdy, když potřebujete operace mimo záběr původní integrace
(VoDZ, správa pověřených osob, archivace, notifikace), sjednocení s aktuálním rozhraním ISDS
(WSDL 3.11, domény `datovka.gov.cz`) nebo aplikaci provozujete na PHP 8.4+ bez `ext-soap`.

## Obsah

- [Instalace](#instalace)
- [Mapa API](#mapa-api)
- [Před → po: přihlášení a načtení přijatých zpráv](#před--po-přihlášení-a-načtení-přijatých-zpráv)
- [Před → po: odeslání datové zprávy](#před--po-odeslání-datové-zprávy)
- [Rozdíly v architektuře](#rozdíly-v-architektuře)
- [Co je v této knihovně navíc](#co-je-v-této-knihovně-navíc)
- [Checklist migrace](#checklist-migrace)

## Instalace

Namespace `Defr\CzechDataBox\*` a `TomasKulhanek\CzechDataBox\*` nekolidují, takže obě knihovny
mohou po dobu migrace koexistovat. Nejdřív tedy přidejte novou a starou odeberte až na konci, kdy
už na ni nic neodkazuje:

```bash
composer require tomas-kulhanek/czech-data-box guzzlehttp/guzzle:^8.0
# … migrace kódu …
composer remove dfridrich/czech-data-box
```

Odebrat starý balíček jako první by rozbilo běžící integraci, dokud není hotová celá migrace.

Poznámky k prostředí:

- Nová knihovna **nevyžaduje `ext-soap`** — SOAP obálky si skládá sama. Vyžaduje `ext-curl`,
  `ext-dom`, `ext-mbstring`, `ext-openssl` a `ext-xml`.
- Vyžaduje **PHP 8.4+**; pokud aplikace běží na starším PHP, je povýšení runtime prvním krokem.
- HTTP klient je volitelný: `guzzlehttp/guzzle ^8.0`, `symfony/http-client 7.*|8.*`, nebo vlastní
  implementace `TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface`.

## Mapa API

Requesty jsou v namespace `TomasKulhanek\CzechDataBox\DTO\Request\*`, odpovědi v
`TomasKulhanek\CzechDataBox\DTO\Response\*` (třídy se jmenují stejně jako operace).
Každá metoda `Connector` má tvar `metoda(Account $account, Request $input): Response`.

| `dfridrich/czech-data-box` | `tomas-kulhanek/czech-data-box` 6.x | Poznámka |
| --- | --- | --- |
| `new DataBox()` | `new Connector(SerializerFactory::create(), GuzzleClientProvider::create())` | konektor je bezstavový, nedrží přihlášení |
| `loginWithUsernameAndPassword($login, $password, $production)` | `new Account()` + `setLoginName()`, `setPassword()`, `setLoginType(LoginTypeEnum::NAME_PASSWORD)` | `Account` se předává do každého volání; prostředí řeší `EndpointProvider`, ne přihlášení |
| `loginWithCertificateAndPassword($certFile, $password, $production)` | `Account::setPkcs12Certificate($obsah, $heslo)` nebo `setPublicKey()` + `setPrivateKey()` + `setPrivateKeyPassPhrase()`, `LoginTypeEnum::SPIS_CERT` / `CERT_LOGIN_NAME_PASSWORD` / `HOSTED_SPIS` | typ přihlášení je explicitní enum |
| `setProductionMode()` / `setTestMode()` | `GuzzleClientProvider::create()` / `GuzzleClientProvider::create(EndpointProvider::test())` | prostředí je vlastnost HTTP providera |
| `DataBoxAccess()->GetOwnerInfoFromLogin(new tDummyInput(null))` | `Connector::getOwnerInfoFromLogin2($account)` | novější varianta operace; starší `getOwnerInfoFromLogin()` je `#[Deprecated]` |
| `DataBoxAccess()->GetUserInfoFromLogin(new tDummyInput(null))` | `Connector::getUserInfoFromLogin2($account)` | dtto |
| `DataBoxAccess()->GetPasswordInfo(new tDummyInput(null))` | `Connector::getPasswordExpirationInfo($account)` | |
| `DmInfoWebService()->GetListOfReceivedMessages(tListOfFReceivedInput)` | `Connector::getListOfReceivedMessages($account, GetListOfReceivedMessages)` | filtr stavů je `FilterEnum`, ne celé číslo |
| `DmInfoWebService()->GetListOfSentMessages(tListOfSentInput)` | `Connector::getListOfSentMessages($account, GetListOfSentMessages)` | |
| `getSimpleApi()->getListOfReceivedMessages($days, $limit)` | ekvivalent není | zkratku s `$days` nahrazuje `setListFrom()` / `setListTo()` s `DateTimeImmutable` |
| `DmOperationsWebService()->MessageDownload(tIDMessInput)` | `Connector::messageDownload($account, MessageDownload)` | |
| `getSimpleApi()->downloadSignedReceivedMessage($dmId)` | `Connector::signedMessageDownload($account, SignedMessageDownload)` | ukládání do souboru řeší aplikace, knihovna vrací data |
| `getSimpleApi()->downloadSignedSentMessage($dmId)` | `Connector::signedSentMessageDownload($account, SignedSentMessageDownload)` | |
| `getSimpleApi()->downloadDeliveryInfo($dmId)` | `Connector::getDeliveryInfo()` / `getSignedDeliveryInfo()` | |
| `getSimpleApi()->getReceivedDataMessageAttachments($dmId)` | `Connector::messageDownload()` → `getReturnedMessage()->getDataMessage()->getFiles()` | přílohy jsou součástí stažené zprávy |
| `DmOperationsWebService()->CreateMessage(tMessageCreateInput)` | `Connector::createMessage($account, CreateMessage)` | posílá se obálka `CreateMultipleMessage`, viz níže |
| `getSimpleApi()->createBasicDataMessage($recipient, $subject, $files)` | ruční složení `Envelope` + `Recipient` + `File` | fasáda nemá 1:1 ekvivalent |
| `DataBoxSearch()->FindDataBox(tFindDBInput)` | `Connector::findDataBox2($account, FindDataBox2)` | `FindDataBox` byla v ISDS nahrazena `FindDataBox2`, knihovna ji od 6.0 nevystavuje |
| `DataBoxSearch()->ISDSSearch2(...)` | `Connector::isdsSearch3($account, ISDSSearch3)` | aktuální varianta vyhledávání dle WSDL 3.11 |
| `DataBoxSearch()->CheckDataBox(...)` | `Connector::checkDataBox($account, CheckDataBox)` | |
| `DataBoxSearch()->GetDataBoxList(...)` | `Connector::getDataBoxList($account, GetDataBoxList)` | |
| `DataBoxAccess()->ChangeISDSPassword(...)` | `Connector::changeIsdsPassword($account, ChangeISDSPassword)` | |
| `DataBoxException` | rozhraní `Exception\CzechDataBoxException` + konkrétní výjimky | viz [Rozdíly v architektuře](#rozdíly-v-architektuře) |
| `IsdsStat()->NumOfMessages(...)`, `getSimpleApi()->getStats()` | ekvivalent není | statistická služba `isds_stat` není v záběru knihovny |
| `RegisterForNotifications` (`src/Api/`) | `Connector::registerForNotifications()`, `getListForNotifications()` | |
| `ArchiveISDSDocument` (`src/Api/`) | `Connector::archiveIsdsDocument($account, ArchiveISDSDocument)` | |
| `UploadAttachment`, `BigMessageDownload` (`src/Api/`) | `Connector::uploadAttachment()`, `createBigMessage()`, `bigMessageDownload()`, … | VoDZ, viz [README](README.md#velkoobjemové-datové-zprávy-vodz) |

## Před → po: přihlášení a načtení přijatých zpráv

**Před** (podle `examples/list_of_received_messages.php` z původní knihovny):

```php
<?php

use Defr\CzechDataBox\Api\tListOfFReceivedInput;
use Defr\CzechDataBox\DataBox;

$dataBox = new DataBox();
$dataBox->loginWithUsernameAndPassword('mujLogin', 'mojeTajneHeslo', true);

$list = new tListOfFReceivedInput();
$list->setDmFromTime(new DateTime('-7 days'))
    ->setDmToTime(new DateTime())
    ->setDmStatusFilter(-1)
    ->setDmLimit(50)
    ->setDmOffset(0);

$messages = $dataBox->DmInfoWebService()
    ->GetListOfReceivedMessages($list)
    ->getDmRecords()
    ->getDmRecord();

foreach ($messages as $record) {
    echo $record->getDmID() . ': ' . $record->getDmAnnotation() . PHP_EOL;
}
```

**Po**:

```php
<?php

use DateTimeImmutable;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfReceivedMessages;
use TomasKulhanek\CzechDataBox\Enum\FilterEnum;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Serializer\SerializerFactory;

$account = new Account();
$account->setLoginName('mujLogin')
    ->setPassword('mojeTajneHeslo')
    ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

$connector = new Connector(SerializerFactory::create(), GuzzleClientProvider::create());

$request = new GetListOfReceivedMessages();
$request->setListFrom(new DateTimeImmutable('-7 days'))
    ->setListTo(new DateTimeImmutable())
    ->setStatusFilter(FilterEnum::DELIVERED, FilterEnum::READ)
    ->setLimit(50)
    ->setOffset(0);

$response = $connector->getListOfReceivedMessages($account, $request);
if (!$response->getStatus()->isOk()) {
    throw new RuntimeException($response->getStatus()->getMessage());
}

foreach ($response->getRecord() as $record) {
    echo $record->getDataMessageId() . ': ' . $record->getAnnotation() . PHP_EOL;
}
```

Na co si dát pozor:

- **Přihlášení není samostatný krok.** `Account` se předává do každého volání; produkční nebo
  testovací prostředí určuje `EndpointProvider` u HTTP providera.
- **Filtr stavů je enum, ne magické číslo.** Místo `setDmStatusFilter(-1)` se předává jedna či více
  hodnot `FilterEnum` (`ALL`, `DELIVERED`, `READ`, `UNDELIVERED`, …); knihovna z nich složí bitovou masku.
- **Časy jsou `DateTimeImmutable`**, ne `DateTime`.
- **Gettery mají české/anglické doménové názvy** místo XSD zkratek: `getDataMessageId()` místo
  `getDmID()`, `getAnnotation()` místo `getDmAnnotation()`.
- **Stav odpovědi je vždy k dispozici** přes `getStatus()->isOk()`, `getCode()` a `getMessage()`.

## Před → po: odeslání datové zprávy

**Před** (fasáda `DataBoxSimpleApi`):

```php
<?php

use Defr\CzechDataBox\DataBox;

$dataBox = new DataBox();
$dataBox->loginWithUsernameAndPassword('mujLogin', 'mojeTajneHeslo', true);

$simpleApi = $dataBox->getSimpleApi();
$message = $simpleApi->createBasicDataMessage(
    'abcdefg',
    'Žádost o vyjádření',
    ['/cesta/zadost.pdf']
);

$result = $simpleApi->sendDataMessage($message);
echo $result->getDmID();
```

**Po**:

```php
<?php

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Serializer\SerializerFactory;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

$account = new Account();
$account->setLoginName('mujLogin')
    ->setPassword('mojeTajneHeslo')
    ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

$connector = new Connector(SerializerFactory::create(), GuzzleClientProvider::create());

$recipient = new Recipient();
$recipient->setDataBoxId('abcdefg');

$envelope = new Envelope();
$envelope->setAnnotation('Žádost o vyjádření');

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
    echo $messageStatus->getDataMessageId() . PHP_EOL;
}
```

Na co si dát pozor:

- **Příjemce není v obálce.** `Connector::createMessage()` volá hromadnou operaci ISDS
  `CreateMultipleMessage` i pro jediného příjemce, takže příjemci se zadávají zvlášť přes
  `Recipient` (`addRecipient()`, 1–50 příjemců). Metoda `createMultipleMessage()` v API neexistuje.
- **Odpověď nemá přímé `dmID`.** Místo `getDmID()` se ID čte z dílčích stavů:
  `getMultipleStatus()` vrací pole `MessageStatus` (jeden na příjemce) a v něm je
  `getDataMessageId()`. `$response->isOk()` je `true`, jsou-li v pořádku všechny dílčí stavy.
- **Přílohu tvoří `File` s povinnými poli** `mimeType`, `metaType` a `description`; právě jedna
  příloha musí mít `metaType` `main`. Obsah se předává jako
  `TomasKulhanek\CzechDataBox\Serializer\SplFileInfo`, takže se soubor streamuje místo načtení
  celého obsahu do řetězce.
- **Limity hlídá knihovna sama** a hlásí je typovanými výjimkami — 1–50 příjemců, max. 100 příloh
  (z toho 10 kontejnerových), součet do 20 MiB, povolené formáty dle vyhlášky č. 194/2009 Sb.,
  délky polí obálky.

## Rozdíly v architektuře

**Bez `ext-soap`.** Původní knihovna staví na `SoapClient` a třídách generovaných z WSDL
(`utilities/classes_generator.php` → `src/Api/t*.php`). Tato knihovna skládá SOAP obálku sama a
posílá ji přes `ClientProviderInterface`:

```php
interface ClientProviderInterface
{
    public function sendRequest(
        Account $account,
        ServiceTypeEnum $serviceType,
        string $xmlBody,
        ?int $maxResponseSize = null
    ): string;
}
```

Přibalené implementace jsou `GuzzleClientProvider` a `SymfonyClientProvider`; vlastní klient
(logování, proxy, retry, PSR-18 adaptér) je otázkou jedné třídy. HTTP klienta lze díky tomu
v testech nahradit mockem — přesně to dělají unit testy knihovny.

**Serializace.** Mapování PHP objektů na XML obstarává `jms/serializer` řízený atributy přímo na
DTO. `SerializerFactory::create()` vrátí nakonfigurovanou instanci.

**Bezstavová identita.** `DataBox` drží přihlášení i aktuální službu v instanci
(`loginWithUsernameAndPassword()`, `setActualService()`). Zde je identita hodnotový objekt
`Account` předávaný do každého volání, takže jeden `Connector` může obsluhovat víc schránek
paralelně bez sdíleného stavu. Hesla a klíče jsou označené `#[\SensitiveParameter]` a
`Account::__debugInfo()` je v dumpu nahrazuje `***`.

**Endpointy.** Adresy neskládá ručně aplikace, ale `EndpointProvider` z domény a
`ServiceTypeEnum` (`dz`, `dx`, `df`, `DsManage`, `vodz`, `arch`) — včetně varianty pro přihlášení
certifikátem (`/cert/…`, `/certds/…`, `/hspis/…`). Výchozí jsou aktuální domény
`datovka.gov.cz` / `datovka-test.gov.cz`.

**Chyby.** Místo jediné `DataBoxException` je k dispozici rozhraní
`TomasKulhanek\CzechDataBox\Exception\CzechDataBoxException`, které implementují všechny výjimky
knihovny — mimo jiné `ConnectionException` (a její potomek `SoapFault` s `faultCode`/`faultString`),
`SystemExclusion` (HTTP 503, servisní okno ISDS), `MissingRequiredField`, `MissingMainFile`,
`DisallowedAttachmentFormat`, `AttachmentCountOverflow`, `RecipientCountOverflow`,
`FileSizeOverflow`, `FieldLengthOverflow`, `InvalidEndpointDomain`, `PkcsCertificateException`.

Stavový kód ISDS v úspěšně doručené odpovědi **výjimku sám o sobě nevyvolá** — čte se přes
`$response->getStatus()`. Kdo chce chybové kódy jako výjimky, použije `Utils\StatusGuard`:

```php
use TomasKulhanek\CzechDataBox\Utils\StatusGuard;

StatusGuard::assertOk($response); // vyhodí IsdsStatusError se statusCode, statusMessage a nápovědou
```

## Co je v této knihovně navíc

Následující operace mohou být důvodem k migraci, protože je aplikace typicky nemůže obejít vlastním
kódem:

- **Velkoobjemové datové zprávy (VoDZ)** do 100 MiB — `uploadAttachment()`, `createBigMessage()`,
  `downloadAttachment()`, `bigMessageDownload()`, `signedBigMessageDownload()`,
  `signedSentBigMessageDownload()`, `authenticateBigMessage()`.
- **Správa vlastní schránky** (`db_manipulations`) — pověřené osoby (`getDataBoxUsers2()`,
  `addDataBoxUser2()`, `updateDataBoxUser2()`, `deleteDataBoxUser2()`), otevřené adresování
  (`setOpenAddressing()`, `clearOpenAddressing()`) a nové přístupové údaje (`newAccessData2()`).
- **Archivace (přerazítkování) ZFO** — `archiveIsdsDocument()`.
- **Notifikace** — `registerForNotifications()`, `getListForNotifications()`.
- **Novější varianty operací podle WSDL 3.11** — `findDataBox2()`, `isdsSearch3()`,
  `getOwnerInfoFromLogin2()`, `getUserInfoFromLogin2()`, `getMessageAuthor2()`, `pdzInfo()`,
  `pdzSendInfo()`, `dtInfo()`, `getDataBoxActivityStatus()`, `eraseMessage()`,
  `getListOfErasedMessages()`, `suspMessageReport()`.
- **XSD validace serializovaných požadavků v testech** proti schématům z přílohy Provozního řádu.
- **Kontrola maximální velikosti odpovědi** (`ResponseSizeLimit`, výchozí 256 MiB) uplatněná už při
  čtení těla, takže nadměrná odpověď nezaplní paměť.

Úplný a strojově ověřovaný přehled je v [matici pokrytí](README.md#pokrytí-webových-služeb-isds).

## Checklist migrace

1. Ověřit, že aplikace běží na **PHP 8.4+**.
2. Přidat `tomas-kulhanek/czech-data-box` a HTTP klienta, starý balíček zatím ponechat.
3. Nahradit přihlášení (`loginWith*`) objektem `Account` a volbou `EndpointProvider`.
4. Převést volání služeb na metody `Connector` podle [mapy API](#mapa-api).
5. Přepsat práci s odpověďmi na nové gettery a kontrolu `getStatus()->isOk()`.
6. Nahradit `catch (DataBoxException)` za `catch (CzechDataBoxException)`, případně doplnit
   `StatusGuard` tam, kde má být chybový kód ISDS výjimkou.
7. Ověřit celý tok proti **testovacímu prostředí** (`EndpointProvider::test()`) dřív, než se
   přepne produkce.
8. Odebrat `dfridrich/czech-data-box` a případně `ext-soap`, pokud ho nic jiného nepoužívá.
9. Projít [Povinnosti aplikace dle Provozního řádu ISDS](README.md#povinnosti-aplikace-dle-provozního-řádu-isds)
   — evidence stažených zpráv, frekvence dotazů a nakládání s přístupovými údaji zůstávají na
   aplikaci bez ohledu na použitou knihovnu.

Narazíte-li při migraci na operaci, která tu chybí nebo se chová jinak, než čekáte, založte prosím
[issue](https://github.com/tomas-kulhanek/czech-data-box/issues).
