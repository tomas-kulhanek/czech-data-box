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
- **`Account::setProduction()` / `isProduction()` odstraněno** — prostředí nově určuje `EndpointProvider` (`EndpointProvider::production()`, `EndpointProvider::test()`, nebo vlastní doména `new EndpointProvider('datovka.cms2.cz')` pro KIVS). HTTP providery přijímají `EndpointProviderInterface`, factory `create()` má volitelný parametr. Vlastní doména se validuje jako holé jméno hostu (bez schématu, přihlašovacích údajů, portu a cesty); neplatná hodnota vyhodí `InvalidEndpointDomain`. Doména musí vždy pocházet z důvěryhodné konfigurace — na výslednou URL se posílají přihlašovací údaje i klientský certifikát.

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
- **XSD validace serializovaných requestů** proti schématům přílohy 1 a 2 Provozního řádu (`tests/_data/xsd/`) — každý request DTO se serializuje a validuje proti `dmBaseTypes.xsd` resp. `dbTypes.xsd`, úplnost pokrytí hlídá reflexní test.
- **Výjimka `SoapFault`** — `Connector` nově detekuje SOAP Fault (1.1 i 1.2) v odpovědi a vyhazuje `SoapFault` (potomek `ConnectionException`) s `faultCode` a `faultString` místo obecné chyby. Všechny výjimky knihovny navíc implementují nový marker interface `CzechDataBoxException`, takže je lze zachytit jedním `catch`.
- **SOAP obálka požadavku se skládá řetězcově bez DOM** — odpadl druhý plný DOM průchod (parse + `importNode` + `saveXml`) nad serializovaným požadavkem; u VoDZ `uploadAttachment()` to šetří ~35 % CPU času a špičkovou paměť nově určuje jen samotná serializace. Horní mez paměti hlídá nový test ve skupině `memory` (z výchozího běhu vyloučena, spouští se `--group memory`).
- **`Utils\StatusGuard`** — volitelný pomocník `assertOk()` / `assertStatusOk()`, který při ne-OK stavu odpovědi vyhodí typovanou výjimku `IsdsStatusError` (s `statusCode`, `statusMessage`, `refNumber`); obsahuje mapu známých kódů ISDS s českým vysvětlením (např. 1281, 1201, 2046). U `CreateMessage` kontroluje i dílčí stavy `dmMultipleStatus`.
- **README**: příklady použití — odeslání VoDZ (`uploadAttachment()` + `createBigMessage()`) a načtení seznamu přijatých zpráv s filtrem.

### Opraveno

- Události doručenky (`GetDeliveryInfo`) se nikdy nedeserializovaly — překlep `dnEventTime` → `dmEventTime` a chybějící namespace u `dmEvents`.
- `PersonalOwnerInfo::$adDistrict` bylo mapováno na element `adStreet`.
- `DTInfo` request posílal `dbID` místo `dbId` (XML je case-sensitive).
- `Response\CheckDataBox` měl XML root `FindDataBoxResponse` místo `CheckDataBoxResponse`.
- Duplicitní prázdný atribut `#[Assert\All()]` v `Delivery` shazoval phpstan.
- Integrační test importoval neexistující třídu `Utils\MessageStatus`.
- Poškozená nebo neúplná SOAP odpověď vypisovala PHP warning z `DOMDocument::loadXML()` a končila prázdnou `ConnectionException`. Parsování nově běží s `libxml_use_internal_errors()` a chyba se hlásí jako `ConnectionException` s popisem.
- Composer skript `check` odkazoval na neexistující `@phpunit` a `check:rector`/`fix:rector` zpracovávaly neexistující složku `public/`; README instaloval Guzzle `^7.0`, dev závislost je `^8.0`.
- Mapování HTTP 503 → `SystemExclusion` nikdy nefungovalo — Guzzle provider testoval symfonní `TransportExceptionInterface`, kterou Guzzle nevyhazuje, a Symfony provider dostával `ServerExceptionInterface`. Oba providery nově vyhodnocují stavový kód odpovědi; HTTP 500 (a další chyby ≥ 400) s neprázdným tělem se navíc předává `Connectoru`, aby šel detekovat SOAP Fault.
- Composer skript `check` neobsahoval testy, takže lokální brána prošla i s rozbitou unit test suite. `check` nově spouští stejnou sadu jako CI (`check:phpstan`, `check:cs`, `check:rector`, `test:unit`); duplicitní `check:all` byl zrušen.
- `composer test:integration` bez přihlašovacích údajů končil chybami místo přeskočení. Integrační testy se nově přeskočí (`markTestSkipped`), pokud chybí proměnné prostředí `*_LOGIN_USER` nebo soubor `.data/cert.pem`.
- Smazán mrtvý `ruleset.xml` — odkazoval na balíček `ninjify/coding-standard`, který v projektu není. Kontrola stylu reálně běží přes `phpcs --standard=PSR12` (skript `check:cs`).
- Dist balíček z Packagistu vezl i vývojové soubory (`tests/`, `.github/`, `phpunit.xml`, `phpstan.neon`, `rector.php`, `Dockerfile`) — `.gitattributes` je nově označuje `export-ignore`. `archive.exclude` v `composer.json` platí jen pro `composer archive`, na distribuci přes Packagist vliv nemá.

### Zabezpečení

- **VoDZ operace nově validují vstup na straně knihovny.** `uploadAttachment()` odmítne prázdný `dmFileDescr`, příponu mimo whitelist vyhlášky č. 194/2009 Sb., chybějící obsah a přílohu nad 100 MB. `createBigMessage()` vyžaduje alespoň jeden `dmExtFile`, hlavní přílohu (`dmFileMetaType="main"`), `dbIDRecipient` a `dmAnnotation`, hlídá limit 100 příloh a souhrnnou velikost inline příloh. Nevalidní požadavek se tak neposílá na server.
- **Limit velikosti odpovědi.** `Connector` odmítne SOAP odpověď větší než `Connector::DEFAULT_MAX_RESPONSE_SIZE` (256 MB) ještě před parsováním; limit lze změnit třetím parametrem konstruktoru.
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

## [5.0.0] – 2024-05-24

Viz [release notes](https://github.com/tomas-kulhanek/czech-data-box/releases/tag/v5.0.0). Starší verze viz [Releases](https://github.com/tomas-kulhanek/czech-data-box/releases).
