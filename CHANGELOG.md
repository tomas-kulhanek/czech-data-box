# Changelog

Formát vychází z [Keep a Changelog](https://keepachangelog.com/cs/1.1.0/), verzování dle [SemVer](https://semver.org/lang/cs/).

## [6.0.0] – zatím nevydáno

Sjednocení knihovny s Provozním řádem ISDS platným od 26. 06. 2026 (WSDL 3.11). Podrobnosti v PR [#33](https://github.com/tomas-kulhanek/czech-data-box/pull/33), [#34](https://github.com/tomas-kulhanek/czech-data-box/pull/34), [#35](https://github.com/tomas-kulhanek/czech-data-box/pull/35), [#40](https://github.com/tomas-kulhanek/czech-data-box/pull/40), [#37](https://github.com/tomas-kulhanek/czech-data-box/pull/37), [#38](https://github.com/tomas-kulhanek/czech-data-box/pull/38) a [#39](https://github.com/tomas-kulhanek/czech-data-box/pull/39).

### ⚠️ Breaking changes

- **Endpointy**: výchozí domény změněny z `mojedatovaschranka.cz` / `czebox.cz` na **`datovka.gov.cz`** / **`datovka-test.gov.cz`**. Staré domény zůstávají funkční minimálně do 31. 12. 2027, knihovna je ale už nepoužívá.
- **`Connector::findDataBox()` odstraněno** — nahrazeno `findDataBox2()` (operace `FindDataBox2`, struktury `OwnerInfoExt2` s `pnGivenNames`, `aifoIsds`, `dbIdOVM`, `dbUpperID` a RUIAN adresou). Stará operace není v dokumentaci ISDS od r. 2018.
- **`Connector::findPersonalDataBox()` odstraněno** (zrušeno v ISDS 2018, nahrazeno FindDataBox2) včetně DTO `PersonalOwnerInfo`.
- **`Connector::confirmDelivery()` odstraněno** — služba v ISDS neexistuje od verze rozhraní 2.33.
- **`ServiceTypeEnum::SUPPLEMENTARY` odstraněno** (duplikát `ACCESS`, nikdy se nevolal).
- **`DataBoxResult`**: `getDataBoxEffectiveOvm(): ?bool` → **`getDataBoxIdOvm(): ?string`** — odpověď `ISDSSearch3` odpovídá `tdbResult2`, kde `dbEffectiveOVM` neexistuje.
- **`Envelope::getPublishOwnId()`** nově vrací `?PublishOwnId` (objekt s hodnotou a atributem `IdLevel`) místo `?bool`; setter dál přijímá i `bool`.
- **Limit velikosti příloh snížen z 25 MiB na 20 MB** (limit běžné datové zprávy dle řádu); do součtu se nově počítají i přílohy `dmXMLContent`.
- **`createMessage()` nově vyhazuje** `AttachmentCountOverflow` (více než 100 příloh, resp. 10 kontejnerových ZIP/ASiC) a `DisallowedAttachmentFormat` (přípona mimo whitelist vyhlášky č. 194/2009 Sb.).
- **Nullabilita dle XSD**: `getAttachmentSize(): ?int` (`MessageRecord`, `ReturnedMessage`, `ReturnedMessageEnvelope`), `DataMessageEvent::getTime(): ?DateTimeImmutable`, `$qTimestamp` nullable.
- **`Traits\GetMainFile::getFiles()` je nově `abstract`** — místo tichého `return []` musí třída, která trait použije, metodu implementovat (všechny DTO v knihovně ji implementují). Dřív zapomenutá implementace znamenala, že `getMainFile()` vždy vrátilo `null`.
- **Nullabilita obálky a dalších odpovědí dle XSD (druhá vlna).** `gMessageEnvelope` označuje `dbIDSender`, `dmSender`, `dmSenderAddress` a `dmRecipient` jako `nillable="true"`, typ `tRecord` má navíc celou sekvenci `minOccurs="0"` a atribut `dmType` je `use="optional"`. Knihovna je přesto měla jako non-nullable bez defaultu, takže jediný takový záznam shodil celé `getListOfReceivedMessages()` chybou `TypeError`/`Error`, kterou aplikace nemohla zachytit přes `catch (ConnectionException)`. Nově vracejí nullable typ:
  - `getSenderId()`, `getSender()`, `getSenderType()`, `getRecipient()` (obálka zprávy — `MessageRecord`, `MessageEnvelope`, `ReceivedMessageEnvelope`, `ReturnedMessage*`),
  - `MessageRecord::getMessageStatus()`, `MessageRecord::getType()`, `ReturnedMessage::getType()`, `ReturnedMessageEnvelope::getType()`,
  - `getDataBoxId()` (trait `DataBoxId` — `OwnerInfo`, `DataBoxResult`, `Response\GetDataBoxActivityStatus` a request DTO, které trait sdílejí),
  - `DataMessageEvent::getDescription()`, `Hash::getValue()`, `Hash::getAlgorithm()`,
  - `Response\GetDeliveryInfo::getDelivery()`, `Response\VerifyMessage::getHash()`, `Response\GetUserInfoFromLogin::getUserInfo()`, `Response\ResignISDSDocument::getDocument()`, `Response\PDZSendInfo::isResult()`.

  Odpovídající settery nově přijímají `null` (rozšíření, ne zúžení — volající kód se měnit nemusí). **`getStatus()` zůstává non-nullable** vědomě: `Response::getStatus(): ResponseStatus` je základní kontrakt knihovny a odpověď bez stavu je stejně nepoužitelná. Request DTO v `src/DTO/Request/` zůstávají non-nullable záměrně — chybějící povinná hodnota je chyba volajícího a má ji odhalit typový systém.
- **HTTP providery už nepředávají původní výjimku klienta jako `previous`.** `GuzzleClientProvider` i `SymfonyClientProvider` nově vyhazují `ConnectionException` / `SystemExclusion` bez zřetězení — `$exception->getPrevious()` vrací `null`. Původní výjimka totiž drží požadavek s hlavičkou `Authorization`, takže plný výpis řetězce výjimek posílal Basic Auth do ISDS rovnou do logu. Diagnostika zůstává zachována: zpráva má nově tvar `Trida\Puvodni\Vyjimky: původní zpráva` (obsahuje metodu, URI i stavový řádek) a stavový kód je dál v `getCode()`.
- **`MissingRequiredField` z HTTP providerů hlásí názvy polí, ne věty.** Zprávy `The required field 'Missing PEM data' is empty.` apod. nahradily `'dbID'`, `'loginName'`, `'password'`, `'publicKey'` a `'privateKey'`. Chybějící jméno a heslo se nově rozlišují a chybějící certifikát se rozlišuje od chybějícího privátního klíče. Testy, které se opíraly o původní znění zprávy, je potřeba upravit.
- **`Envelope::setRecipientOrgUnit()` / `getRecipientOrgUnit()` / `setRecipientOrgUnitNum()` / `getRecipientOrgUnitNum()` odstraněno.** `createMessage()` odesílá hromadnou operaci `CreateMultipleMessage`, jejíž obálka odpovídá XSD typu `tMultipleMessageEnvelopeSub` — ten prvky `dmRecipientOrgUnit` ani `dmRecipientOrgUnitNum` nezná (adresáti jsou v `dmRecipients`/`tRecipients`). Dosud tyto settery tiše generovaly XML nevalidní proti `dmBaseTypes.xsd`. Trait `DataMessageEnvelopeSub` byl rozdělen: sdílené prvky jsou nově v `Traits\MultipleMessageEnvelopeSub` (XSD skupina `gMultipleMessageEnvelopeSub`, používá `Envelope`), zatímco `Traits\DataMessageEnvelopeSub` je doplňuje o organizační jednotku příjemce pro obálky jedné zprávy (XSD skupina `gMessageEnvelopeSub`, používá `MessageEnvelope`, `ReceivedMessageEnvelope` a `MessageRecord`).
- **`Account::setProduction()` / `isProduction()` odstraněno** — prostředí nově určuje `EndpointProvider` (`EndpointProvider::production()`, `EndpointProvider::test()`, nebo vlastní doména `new EndpointProvider('datovka.cms2.cz')` pro KIVS). HTTP providery přijímají `EndpointProviderInterface`, factory `create()` má volitelný parametr. Vlastní doména se validuje jako holé jméno hostu (bez schématu, přihlašovacích údajů, portu a cesty); neplatná hodnota vyhodí `InvalidEndpointDomain`. Doména musí vždy pocházet z důvěryhodné konfigurace — na výslednou URL se posílají přihlašovací údaje i klientský certifikát.
- **`symfony/validator` už není povinná závislost** — přesunut z `require` do `require-dev` a `suggest`. Knihovna validátor nikdy nespouštěla (atributy `#[Assert\*]` na request DTO jsou metadata pro konzumenta), constraint `^7.0` přitom blokoval instalaci v aplikacích na Symfony 8. Pokud validátor v aplikaci používáte, doinstalujte si ho explicitně (`composer require symfony/validator`); atributy zůstávají beze změny. Vlastní kontroly knihovny (limity a formáty příloh, povinná pověření) fungují dál nezávisle na něm.

### Přidáno

- **Nové operace dm_info**: `sentMessageEnvelopeDownload()`, `getMessageAuthor2()`, `eraseMessage()`, `getListOfErasedMessages()` + `pickUpAsyncResponse()`, `getListForNotifications()`, `registerForNotifications()`, `suspMessageReport()` (nahlášení podezřelé zprávy).
- **Nové operace dm_operations**: `dummyOperation()` (keep-alive).
- **Nové operace db_search**: `getConstants()`, `getDataBoxAddress()`.
- **Nové operace db_access**: `getOwnerInfoFromLogin2()`, `getUserInfoFromLogin2()` (rozšířené údaje: `aifoIsds`, `isdsID`, `dbIdOVM`, RUIAN adresa).
- **Správa uživatelů schránky (db_manipulations)**: `getDataBoxUsers2()`, `addDataBoxUser2()`, `updateDataBoxUser2()`, `deleteDataBoxUser2()` — výpis a správa pověřených osob vlastní schránky (vyžaduje oprávnění `PRIVIL_OWNER_ADM`). `addDataBoxUser2()` podporuje i virtuální obálku (`dbVirtual`) s e-mailem pro odkaz na Aktivační portál.
- **Velkoobjemové datové zprávy (VoDZ, do 100 MB)** přes SOAP 1.2 na endpointech `ws2[c].…/DS/vodz`: `uploadAttachment()`, `downloadAttachment()`, `createBigMessage()`, `authenticateBigMessage()`, `bigMessageDownload()`, `signedBigMessageDownload()`, `signedSentBigMessageDownload()`.
- **Archivace**: `archiveIsdsDocument()` (přerazítkování ZFO) na `ws2[c].…/DS/arch`.
- **Nové atributy DTO**: `specMessFlag` + `isSuspicious()` (podezřelá zpráva), `dmVODZ` + `isVodz()` a `attsNum` (rozpoznání VoDZ v seznamech), `PublishOwnId` s `IdLevel`.
- **Nová pole DTO**: `pnLastNameAtBirth`, `caState`, `adDistrict`, `adAMCode`.
- **`Utils\AllowedAttachmentFormats`** — whitelist 51 povolených přípon vč. kontejnerových formátů.
- **Týdenní integrační běh proti `datovka-test.gov.cz`** — testy rozděleny na suite `unit` a `integration` (composer skripty `test:unit` / `test:integration`); nové workflow `integration.yml` spouští integrační testy každé pondělí v 07:00 UTC (mimo servisní okno ISDS) a lze jej spustit i ručně.
- **XSD validace serializovaných requestů** proti schématům přílohy 1 a 2 Provozního řádu (`tests/_data/xsd/`) — každý request DTO se serializuje a validuje proti `dmBaseTypes.xsd` resp. `dbTypes.xsd`, úplnost pokrytí hlídá reflexní test. `CreateMultipleMessage` se navíc validuje i s **plně naplněnou** obálkou a samostatný test porovnává prvky deklarované na `Envelope` s prvky povolenými typem `tMultipleMessageEnvelopeSub`.
- **Výjimka `SoapFault`** — `Connector` nově detekuje SOAP Fault (1.1 i 1.2) v odpovědi a vyhazuje `SoapFault` (potomek `ConnectionException`) s `faultCode` a `faultString` místo obecné chyby. Všechny výjimky knihovny navíc implementují nový marker interface `CzechDataBoxException`, takže je lze zachytit jedním `catch`.
- **SOAP obálka požadavku se skládá řetězcově bez DOM** — odpadl druhý plný DOM průchod (parse + `importNode` + `saveXml`) nad serializovaným požadavkem; u VoDZ `uploadAttachment()` to šetří ~35 % CPU času a špičkovou paměť nově určuje jen samotná serializace. Horní mez paměti hlídá nový test ve skupině `memory` (z výchozího běhu vyloučena, spouští se `--group memory`).
- **`Utils\StatusGuard`** — volitelný pomocník `assertOk()` / `assertStatusOk()`, který při ne-OK stavu odpovědi vyhodí typovanou výjimku `IsdsStatusError` (s `statusCode`, `statusMessage`, `refNumber`); obsahuje mapu známých kódů ISDS s českým vysvětlením (např. 1281, 1201, 2046). U `CreateMessage` kontroluje i dílčí stavy `dmMultipleStatus`.
- **Kontrola délkových limitů obálky dle XSD.** `createMessage()` i `createBigMessage()` nově odmítnou příliš dlouhý `dmAnnotation` (255 znaků), `dmSenderRefNumber` / `dmRecipientRefNumber` a `dmSenderIdent` / `dmRecipientIdent` (50 znaků) novou výjimkou `FieldLengthOverflow` ještě před odesláním. Délka se počítá ve znacích, ne v bajtech. Limity jsou dostupné jako konstanty `Connector::MAX_ANNOTATION_LENGTH`, `MAX_REF_NUMBER_LENGTH` a `MAX_IDENT_LENGTH`.
- **README**: příklady použití — odeslání VoDZ (`uploadAttachment()` + `createBigMessage()`) a načtení seznamu přijatých zpráv s filtrem.
- **Generický test nullability response DTO** (`tests/Unit/DtoNullabilityTest.php`) — pro každé response DTO se deserializuje nejmenší dokument, který schéma připouští, a reflexí se zavolají všechny veřejné gettery; žádný nesmí skončit `Error`. Druhá varianta posílá `xsi:nil="true"` na každý prvek, který je ve schématu nillable. Doplňuje ho statická kontrola, která čte `dmBaseTypes.xsd` / `dbTypes.xsd` a vyžaduje nullable vlastnost všude, kde je prvek ve schématu volitelný — vědomé výjimky jsou vyjmenované v testu i s odůvodněním, takže se na ně nedá zapomenout.
- **Unit testy obou HTTP providerů** (`ClientProviderAuthenticationTest`) nad `GuzzleHttp\Handler\MockHandler` a `Symfony\Component\HttpClient\MockHttpClient` — hlídají shodnou hlavičku `Authorization` u všech hodnot `LoginTypeEnum`, hlavičky `Content-Type` / `SOAPAction` pro SOAP 1.1 i 1.2, odmítnutí neúplných pověření ještě před odesláním requestu a mapování HTTP 503 na `SystemExclusion`.

### Opraveno

- Události doručenky (`GetDeliveryInfo`) se nikdy nedeserializovaly — překlep `dnEventTime` → `dmEventTime` a chybějící namespace u `dmEvents`.
- `PersonalOwnerInfo::$adDistrict` bylo mapováno na element `adStreet`.
- `DTInfo` request posílal `dbID` místo `dbId` (XML je case-sensitive).
- `Response\CheckDataBox` měl XML root `FindDataBoxResponse` místo `CheckDataBoxResponse`.
- Duplicitní prázdný atribut `#[Assert\All()]` v `Delivery` shazoval phpstan.
- Integrační test importoval neexistující třídu `Utils\MessageStatus`.
- **SOAP odpověď s neprefixovanou obálkou** (výchozí namespace, tj. `<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">`) se nedala zpracovat — tělo se hledalo výrazem složeným z prefixu kořenového elementu, takže u prázdného prefixu vznikl neplatný XPath `//:Body`, unikl PHP warning a operace skončila `ConnectionException: The response is empty`. Tělo se nově hledá přes registrované namespace SOAP 1.1 i 1.2, tedy nezávisle na prefixu odesílatele.
- Poškozená nebo neúplná SOAP odpověď vypisovala PHP warning z `DOMDocument::loadXML()` a končila prázdnou `ConnectionException`. Parsování nově běží s `libxml_use_internal_errors()` a chyba se hlásí jako `ConnectionException` s popisem.
- Composer skript `check` odkazoval na neexistující `@phpunit` a `check:rector`/`fix:rector` zpracovávaly neexistující složku `public/`; README instaloval Guzzle `^7.0`, dev závislost je `^8.0`.
- **Každý HTTP provider skládal autentizaci jinak, takže výměna HTTP klienta měnila chování přihlášení.** `SymfonyClientProvider` posílal u typu přihlášení `HOSTED_SPIS` hlavičku `Basic base64("dbID")` bez dvojtečky (dle RFC 7617 nevalidní; Guzzle správně posílá `base64("dbID:")`) a při chybějícím `dbID`, přihlašovacím jménu nebo hesle request stejně odeslal — s `Basic base64("")`, resp. `Basic base64(":")` — a zbytečně čerpal limit neúspěšných pokusů o přihlášení. Skládání hlaviček i pověření je nově v jedné sdílené službě `Provider\RequestOptionsFactory`, kterou si oba providery berou v konstruktoru (včetně kontrol na chybějící údaje, které vyhodí `MissingRequiredField` ještě před odesláním), takže oba providery posílají bajt po bajtu stejné hlavičky. Symfony provider navíc používá nativní volbu `auth_basic` místo ručního `base64_encode`.
- `SoapFault` jako jediná výjimka knihovny neimplementovala marker interface `CzechDataBoxException` explicitně (dědila jej pouze přes `ConnectionException`). Nově jej deklaruje sama; úplnost hlídá reflexní test nad `src/Exception/`.
- Mapování HTTP 503 → `SystemExclusion` nikdy nefungovalo — Guzzle provider testoval symfonní `TransportExceptionInterface`, kterou Guzzle nevyhazuje, a Symfony provider dostával `ServerExceptionInterface`. Oba providery nově vyhodnocují stavový kód odpovědi; HTTP 500 (a další chyby ≥ 400) s neprázdným tělem se navíc předává `Connectoru`, aby šel detekovat SOAP Fault.
- Composer skript `check` neobsahoval testy, takže lokální brána prošla i s rozbitou unit test suite. `check` nově spouští stejnou sadu jako CI (`check:phpstan`, `check:cs`, `check:rector`, `test:unit`); duplicitní `check:all` byl zrušen.
- `composer test:integration` bez přihlašovacích údajů končil chybami místo přeskočení. Integrační testy se nově přeskočí (`markTestSkipped`), pokud chybí proměnné prostředí `*_LOGIN_USER` nebo soubor `.data/cert.pem`.
- Smazán mrtvý `ruleset.xml` — odkazoval na balíček `ninjify/coding-standard`, který v projektu není. Kontrola stylu reálně běží přes `phpcs --standard=PSR12` (skript `check:cs`).
- **`jms/serializer` je nově explicitní závislost** (`^3.32`). `Connector::__construct()` přijímá `JMS\Serializer\SerializerInterface`, takže jde o součást veřejného API — dosud se balíček instaloval jen tranzitivně přes `tomas-kulhanek/serializer`.
- Constraint PHP zpřísněn z `>=8.4` na `^8.4`, aby se knihovna neinstalovala na dosud nevydané PHP 9.
- `GuzzleClientProvider::__construct()` přijímá `GuzzleHttp\ClientInterface` místo konkrétní třídy `GuzzleHttp\Client` — stejně jako `SymfonyClientProvider` s `HttpClientInterface`. Umožňuje předat dekorovaného či mockovaného klienta.
- `setRecipientId()` a `setToHands()` v obálce stažené zprávy vracely `void`, takže se fluent řetězec setterů uprostřed rozbil. Nově vracejí `self` jako ostatní settery.
- `DTO\Recipient` posílal do každého požadavku prázdný element `<p:dmToHands></p:dmToHands>`; pole `dmToHands` je nově označeno `#[Serializer\SkipWhenEmpty]`.
- `Utils\BinarySuffix` ořezával desetinnou část (`%d`), takže hláška o překročení limitu tvrdila „Maximum size … can be 20 MB. Current size is 20 MB." i pro 20,9 MB. Velikosti se nově formátují s jedním desetinným místem.
- Dist balíček z Packagistu vezl i vývojové soubory (`tests/`, `.github/`, `phpunit.xml`, `phpstan.neon`, `rector.php`, `Dockerfile`) — `.gitattributes` je nově označuje `export-ignore`. `archive.exclude` v `composer.json` platí jen pro `composer archive`, na distribuci přes Packagist vliv nemá.

### Zabezpečení

- **VoDZ operace nově validují vstup na straně knihovny.** `uploadAttachment()` odmítne prázdný `dmFileDescr`, příponu mimo whitelist vyhlášky č. 194/2009 Sb., chybějící obsah a přílohu nad 100 MB. `createBigMessage()` vyžaduje alespoň jeden `dmExtFile`, hlavní přílohu (`dmFileMetaType="main"`), `dbIDRecipient` a `dmAnnotation`, hlídá limit 100 příloh a souhrnnou velikost inline příloh. Nevalidní požadavek se tak neposílá na server.
- **Explicitní hardening XML parseru.** `DOMDocument::loadXML()` se v `Connectoru` volá s `LIBXML_NONET` (zákaz síťových požadavků při parsování) a nikdy s `LIBXML_NOENT`, takže se externí entity nedosazují. Chování zamykají regresní testy: odpověď s XXE (`<!ENTITY xxe SYSTEM "file:///etc/passwd">`) i s rekurzivní entitou („billion laughs“) končí `ConnectionException` a obsah souboru se nikdy nedostane do DTO.
- **Limit velikosti odpovědi.** `Connector` odmítne SOAP odpověď větší než `Connector::DEFAULT_MAX_RESPONSE_SIZE` (256 MB) ještě před parsováním; limit lze změnit třetím parametrem konstruktoru.
- **Pověření neunikají do stack trace.** `Account::setPassword()`, `setPrivateKey()`, `setPrivateKeyPassPhrase()` a hlavně `setPkcs12Certificate()` (oba parametry) mají nově atribut `#[\SensitiveParameter]`. Při výchozím `zend.exception_ignore_args=0` nesla `PkcsCertificateException` vyhozená z `setPkcs12Certificate()` ve stack trace binární obsah PKCS#12 **i jeho heslo**, takže při odeslání výjimky do Sentry nebo Monologu utekl celý certifikát. Argumenty se nyní v trace nahrazují objektem `SensitiveParameterValue`.
- **`Account::__debugInfo()`** maskuje `password`, `privateKey` a `privateKeyPassPhrase` hodnotou `***`, takže `var_dump()`, `print_r()` ani `dd()` heslo neukážou. Ostatní vlastnosti (`loginName`, `dataBoxId`, `loginType`, `publicKey`) zůstávají čitelné. Pozor: `var_export()` a `serialize()` takový háček nemají — účet do logu neposílejte.
- **Původní výjimky HTTP klienta se nezřetězují** (viz Breaking changes) — Basic Auth do ISDS už neunikne do výpisu řetězce výjimek.
- **`SECURITY.md`** — postup pro neveřejné hlášení zranitelností (GitHub Security Advisory nebo e-mail), podporované verze, očekávané lhůty a doporučení pro bezpečné použití knihovny.
- **GitHub Actions pinovány na commit SHA** a hlavní workflow má minimální `permissions: contents: read`. Přibyl job `Dependency audit` s `composer validate --strict` a `composer audit`, který běží i v týdenním scheduled runu.

### Migrace z 5.x

1. `findDataBox(new FindDataBox())` → `findDataBox2(new FindDataBox2())`; místo `OwnerInfo` naplňte `OwnerInfoExt2` (jména přes `setGivenNames()` místo first/middle name).
2. `findPersonalDataBox()` → `findDataBox2()` s vyplněnými osobními údaji.
3. Volání `confirmDelivery()` odstraňte bez náhrady (služba v ISDS neexistuje).
4. `getDataBoxEffectiveOvm()` → `getDataBoxIdOvm()` (identifikátor OVM z Rejstříku OVM, `?string`).
5. `getPublishOwnId()` vrací objekt: booleovskou hodnotu čtěte přes `->getValue()`.
6. Zprávy nad 20 MB odesílejte přes VoDZ (`uploadAttachment()` + `createBigMessage()`).
7. Počítejte s novými výjimkami `AttachmentCountOverflow` a `DisallowedAttachmentFormat` u `createMessage()`.
8. `$account->setProduction(false)` → `GuzzleClientProvider::create(EndpointProvider::test())` (resp. `SymfonyClientProvider::create(...)`); volání `setProduction()` odstraňte.
9. `getOwnerInfoFromLogin()` / `getUserInfoFromLogin()` jsou deprecated — přejděte na `getOwnerInfoFromLogin2()` / `getUserInfoFromLogin2()`.
10. `uploadAttachment()` a `createBigMessage()` nově vyhazují `MissingRequiredField`, `MissingMainFile`, `DisallowedAttachmentFormat`, `AttachmentCountOverflow` a `FileSizeOverflow` — ošetřete je stejně jako u `createMessage()`.
11. Pokud ve své aplikaci validujete request DTO přes `symfony/validator`, přidejte si ho do vlastního `composer.json` (`composer require symfony/validator`) — knihovna ho už netáhne tranzitivně.
10. Pokud jste z `ConnectionException` / `SystemExclusion` četli `getPrevious()` (například kvůli `GuzzleHttp\Exception\RequestException::getResponse()`), přejděte na `getCode()` a `getMessage()` — původní výjimka se už záměrně nepředává, aby s sebou nenesla hlavičku `Authorization`.
11. `uploadAttachment()` a `createBigMessage()` nově vyhazují `MissingRequiredField`, `MissingMainFile`, `DisallowedAttachmentFormat`, `AttachmentCountOverflow` a `FileSizeOverflow` — ošetřete je stejně jako u `createMessage()`.
11. `Envelope::setRecipientOrgUnit()` → `Recipient::setOrgUnit()` (obdobně `Envelope::setRecipientOrgUnitNum()` → `Recipient::setOrgUnitNum()`). Organizační jednotka příjemce se u `createMessage()` zadává per adresát, ne na obálce.

## [5.0.0] – 2024-05-24

Viz [release notes](https://github.com/tomas-kulhanek/czech-data-box/releases/tag/v5.0.0). Starší verze viz [Releases](https://github.com/tomas-kulhanek/czech-data-box/releases).
