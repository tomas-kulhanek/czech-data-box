# Migrace z `tomas-kulhanek/czech-data-box` 5.0 na 6.0

Tento soubor je návod pro **LLM agenta i pro člověka**, který má povýšit aplikaci ze závislosti
`tomas-kulhanek/czech-data-box:^5.0` na `^6.0`. Úplný seznam změn je v [CHANGELOG.md](CHANGELOG.md);
tady je jen to, co se musí udělat ve volajícím kódu, v pořadí, ve kterém to dává smysl dělat.

## Instrukce pro agenta

- **Vstup**: aplikace používající `tomas-kulhanek/czech-data-box` verze 5.0.x.
- **Výstup**: tatáž aplikace na verzi 6.0.x, se stejným chováním.
- **Pravidlo**: měň jen to, co si vynutila změna API knihovny. Nepřepisuj okolní kód, neměň
  formátování nesouvisejících řádků, nepřidávej abstrakce a nesjednocuj styl.
- **Postup**: projdi kapitoly 1–9 popořadě. Každá začíná grepem, kterým zjistíš, jestli se tě
  vůbec týká. Když grep nic nenajde, kapitolu přeskoč.
- **Nejistota**: pokud u konkrétního místa nevíš, jestli se má měnit (typicky u kapitoly 5,
  nullability), **změnu neprováděj** a zapiš místo do závěrečného soupisu pro člověka.
  Špatně domyšlený `?? ''` umí zamaskovat chybu, kterou by jinak odhalil typový systém.
- **Kontrola**: na závěr projdi [kontrolní seznam](#10-kontrolní-seznam-na-závěr).

---

## 1. Runtime a závislosti

```bash
grep -n '"php"\|czech-data-box\|guzzlehttp/guzzle\|symfony/http-client\|symfony/validator\|tomas-kulhanek/serializer' composer.json
```

| Co | 5.0 | 6.0 |
| --- | --- | --- |
| PHP | `>=8.2` | **`^8.4`** |
| `guzzlehttp/guzzle` | `^7.8` | **`^8.0`** |
| `symfony/http-client` | `5.4.*\|6.*\|7.*` | **`7.*\|8.*`** |
| `symfony/validator` | povinná závislost knihovny | **není** — doinstalujte si ji sami, pokud request DTO validujete |
| `tomas-kulhanek/serializer` | povinná závislost knihovny | **není** — serializer je součástí knihovny (viz kapitola 3) |
| `jms/serializer` | jen tranzitivně | **explicitní závislost** (`^3.32`) |
| `composer/ca-bundle` | — | **nová závislost** (`^1.5`), viz kapitola 7 |

Kroky:

1. Povyšte runtime na PHP 8.4. Knihovna se na 8.2 a 8.3 nenainstaluje.
2. `composer require tomas-kulhanek/czech-data-box:^6.0`
3. Pokud používáte Guzzle: `composer require guzzlehttp/guzzle:^8.0`.
4. Pokud ve své aplikaci voláte `symfony/validator` na request DTO knihovny
   (atributy `#[Assert\*]` na nich zůstávají): `composer require symfony/validator`.
   Knihovna validátor sama nikdy nespouštěla, jen ho tahala jako závislost.
5. `tomas-kulhanek/serializer` ze svého `composer.json` odstraňte, pokud ho nepoužíváte jinde.

Symfony 8 nyní jde nainstalovat vedle knihovny; dřív to blokoval constraint `symfony/yaml`
tažený přes `tomas-kulhanek/serializer`.

## 2. Volba prostředí se přesunula z `Account` do `EndpointProvider`

```bash
grep -rn 'setProduction\|isProduction' --include=*.php .
```

`Account::setProduction()` a `Account::isProduction()` **byly odstraněny**. Prostředí určuje
provider endpointů, který se předává HTTP provideru:

```php
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;

// 5.0
$account->setProduction(false);
$provider = GuzzleClientProvider::create();

// 6.0
$provider = GuzzleClientProvider::create(EndpointProvider::test());

// produkce (výchozí, parametr lze vynechat)
$provider = GuzzleClientProvider::create(EndpointProvider::production());

// vlastní doména, například KIVS
$provider = GuzzleClientProvider::create(new EndpointProvider('datovka.cms2.cz'));
```

Totéž platí pro `SymfonyClientProvider::create()`.

Zároveň se změnily **výchozí domény**: `mojedatovaschranka.cz` → `datovka.gov.cz`
a `czebox.cz` → `datovka-test.gov.cz`. Staré domény ISDS provozuje minimálně do 31. 12. 2027,
ale knihovna je už nepoužívá. Pokud máte v aplikaci allowlist odchozích hostů, firewallové
pravidlo nebo záznam v konfiguraci proxy, doplňte tam nové domény.

Vlastní doména se validuje jako holé jméno hostu — bez schématu, přihlašovacích údajů, portu
a cesty. Neplatná hodnota vyhodí `Exception\InvalidEndpointDomain`. Doména musí vždy pocházet
z důvěryhodné konfigurace: na výslednou URL se posílají přihlašovací údaje i klientský certifikát.

## 3. Mechanická přejmenování

Následující náhrady jsou čistě textové a jde je udělat hromadně. Vždy nahrazujte **celý
plně kvalifikovaný název**, ne jen krátké jméno třídy — krátká jména jako `Request`
nebo `Response` se v aplikacích běžně vyskytují i mimo tuto knihovnu.

### 3.1 Základní typy DTO (odpadl prefix `I`)

```bash
grep -rn 'IRequest\|IResponse\|IResponseStatus' --include=*.php .
```

| 5.0 | 6.0 |
| --- | --- |
| `TomasKulhanek\CzechDataBox\DTO\Request\IRequest` | `TomasKulhanek\CzechDataBox\DTO\Request\Request` |
| `TomasKulhanek\CzechDataBox\DTO\Response\IResponse` | `TomasKulhanek\CzechDataBox\DTO\Response\Response` |
| `TomasKulhanek\CzechDataBox\DTO\Response\IResponseStatus` | `TomasKulhanek\CzechDataBox\DTO\Response\ResponseStatus` |

Jde o rozhraní, takže se změna projeví jen v typehintech, `instanceof`, `@param`/`@return`
anotacích a ve vlastních implementacích DTO. Metody ani chování se nemění.

### 3.2 Serializer

```bash
grep -rn 'TomasKulhanek\\\\Serializer\|isTemp()' --include=*.php .
```

| 5.0 | 6.0 |
| --- | --- |
| `TomasKulhanek\Serializer\SerializerFactory` | `TomasKulhanek\CzechDataBox\Serializer\SerializerFactory` |
| `TomasKulhanek\Serializer\Utils\SplFileInfo` | `TomasKulhanek\CzechDataBox\Serializer\SplFileInfo` |
| `SplFileInfo::isTemp()` | `SplFileInfo::isTemporary()` |

`SerializerFactory::create()` nově deklaruje návratový typ `JMS\Serializer\SerializerInterface`.
`SplFileInfo::createInTemp()` a `getContents()` nově vyhodí `RuntimeException` místo tichého
`false`; deserializace prázdného nebo nevalidního base64 vrací `null`.

### 3.3 Přejmenované metody DTO

```bash
grep -rn 'getDataBoxEffectiveOvm\|setDataBoxEffectiveOvm' --include=*.php .
```

| 5.0 | 6.0 |
| --- | --- |
| `DTO\DataBoxResult::getDataBoxEffectiveOvm(): ?bool` | `DTO\DataBoxResult::getDataBoxIdOvm(): ?string` |
| `DTO\DataBoxResult::setDataBoxEffectiveOvm(?bool)` | `DTO\DataBoxResult::setDataBoxIdOvm(?string)` |

Nejde jen o přejmenování: odpověď `ISDSSearch3` odpovídá XSD typu `tdbResult2`, kde prvek
`dbEffectiveOVM` vůbec neexistuje. Nová metoda vrací **identifikátor OVM z Rejstříku OVM**
(řetězec), ne booleovský příznak. Kód typu `if ($result->getDataBoxEffectiveOvm())` proto
nelze přepsat mechanicky — rozhodněte, jestli se ptáte na „je to OVM" (pak
`$result->getDataBoxIdOvm() !== null`) nebo potřebujete samotný identifikátor.

### 3.4 Traity

```bash
grep -rn 'CzechDataBox\\\\Traits' --include=*.php .
```

Adresář `src/Traits/` je zrušen, knihovna nepoužívá jediný trait. Pokud jste některý z nich
použili ve vlastní třídě, zkopírujte si jeho poslední podobu z tagu `v5.0.0` do svého kódu,
nebo dědit z odpovídající abstraktní třídy knihovny:

| Trait v 5.0 | Náhrada v 6.0 |
| --- | --- |
| `Traits\DataMessageStatus` | `DTO\Response\DataMessageResponse` |
| `Traits\DataBoxStatus` | `DTO\Response\DataBoxResponse` |
| `Traits\Signature` | `DTO\Response\SignedDataMessageResponse` |
| `Traits\DataMessageId` | `DTO\Request\DataMessageRequest` |
| `Traits\DataBoxId` | `DTO\Request\DataBoxRequest` |
| `Traits\ExtApproval` | `DTO\Request\DataBoxManagementRequest` |
| `Traits\Dummy` | `DTO\Request\DummyRequest` |
| `Traits\StatusFilter` | `DTO\Request\MessageListRequest` |
| `Traits\DataMessageEnvelope`, `Traits\DataMessageEnvelopeSub` | `DTO\AbstractMessageEnvelope` |
| `Traits\Address`, `Traits\PersonName` | `DTO\PersonInfo` |
| `Traits\GetMainFile` | `Utils\MainFileResolver::resolve(File[]): ?File` (kompozice, ne dědičnost) |
| `Traits\QTimestamp` | vlastnost je nově přímo v `DTO\Delivery`, `DTO\ReturnedMessage`, `DTO\ReturnedMessageEnvelope` |

Pro běžného konzumenta se veřejné API dotčených DTO nemění. Jediný rozdíl: fluent settery
vracejí `static` místo `self`, což je pro volající kód rozšíření, ne zúžení.

## 4. Odstraněné operace a metody

```bash
grep -rn 'findDataBox(\|findPersonalDataBox(\|confirmDelivery(\|SUPPLEMENTARY' --include=*.php .
```

### 4.1 `findDataBox()` → `findDataBox2()`

Stará operace není v dokumentaci ISDS od roku 2018. Odstraněny jsou i DTO
`DTO\Request\FindDataBox` a `DTO\Response\FindDataBox`.

```php
// 5.0
$request = new FindDataBox();
$request->setOwnerInfo(
    (new OwnerInfo())->setFirstName('Jan')->setMiddleName('Karel')->setLastName('Novák')
);
$response = $connector->findDataBox($account, $request);
foreach ($response->getResult() as $box) { /* OwnerInfo */ }

// 6.0
$request = new FindDataBox2();
$request->setOwnerInfo(
    (new OwnerInfoExt2())->setGivenNames('Jan Karel')->setLastName('Novák')
);
$response = $connector->findDataBox2($account, $request);
foreach ($response->getResult() as $box) { /* OwnerInfoExt2 */ }
```

Pozor na jména: `OwnerInfoExt2` má místo `setFirstName()` / `setMiddleName()` jediné
`setGivenNames()` (prvek `pnGivenNames`). Navíc nese `aifoIsds`, `dbIdOVM`, `dbUpperID`
a RUIAN adresu.

### 4.2 `findPersonalDataBox()` → `findDataBox2()`

Operace byla v ISDS zrušena v roce 2018. Odstraněno je i DTO `DTO\PersonalOwnerInfo`
a `DTO\Request\FindPersonalDataBox` / `DTO\Response\FindPersonalDataBox`. Vyhledání fyzické
osoby proveďte přes `findDataBox2()` s vyplněnými osobními údaji v `OwnerInfoExt2`
(`dbType`, jméno, datum a místo narození).

### 4.3 `confirmDelivery()` — bez náhrady

Služba v ISDS neexistuje od verze rozhraní 2.33. Volání odstraňte, odstraněna jsou i DTO
`DTO\Request\ConfirmDelivery` a `DTO\Response\ConfirmDelivery`.

### 4.4 `ServiceTypeEnum::SUPPLEMENTARY` — bez náhrady

Byl to duplikát `ServiceTypeEnum::ACCESS` (obojí mapovalo na endpoint `DsManage`) a knihovna
ho nikdy nepoužila. Případný výskyt nahraďte `ServiceTypeEnum::ACCESS`.

### 4.5 Organizační jednotka příjemce se zadává per adresát

```bash
grep -rn 'setRecipientOrgUnit\|getRecipientOrgUnit' --include=*.php .
```

`Envelope::setRecipientOrgUnit()`, `getRecipientOrgUnit()`, `setRecipientOrgUnitNum()`
a `getRecipientOrgUnitNum()` byly odstraněny. `createMessage()` odesílá hromadnou operaci
`CreateMultipleMessage`, jejíž obálka odpovídá XSD typu `tMultipleMessageEnvelopeSub` — a ten
prvky `dmRecipientOrgUnit` ani `dmRecipientOrgUnitNum` nezná. Dosud tyto settery tiše
generovaly XML nevalidní proti schématu.

```php
// 5.0
$envelope->setRecipientOrgUnit('Odbor dopravy')->setRecipientOrgUnitNum(42);

// 6.0
$recipient->setOrgUnit('Odbor dopravy')->setOrgUnitNum(42);
```

Obálky **stažených** zpráv (`MessageEnvelope`, `ReceivedMessageEnvelope`, `MessageRecord`)
`getRecipientOrgUnit()` dál mají — tam prvek ve schématu je. Zdědily ho z `DTO\AbstractMessageEnvelope`.

## 5. Nullabilita odpovědí podle XSD

Tohle je jediná kapitola, která vyžaduje úsudek, ne mechanickou náhradu. **U nejasných míst
nic neměňte a nahlaste je.**

Řada getterů odpovědních DTO byla non-nullable, přestože je XSD označuje jako `nillable="true"`,
`minOccurs="0"` nebo `use="optional"`. Jediný takový záznam v odpovědi shodil celé volání
`TypeError`em, který aplikace nemohla zachytit přes `catch (ConnectionException)`. Nově vracejí
nullable typ:

| Třída / skupina tříd | Gettery |
| --- | --- |
| Obálka zprávy (`MessageRecord`, `MessageEnvelope`, `ReceivedMessageEnvelope`, `ReturnedMessage`, `ReturnedMessageEnvelope`) | `getSenderId()`, `getSender()`, `getSenderType()`, `getRecipient()`, `getType()` |
| `MessageRecord` | `getMessageStatus()`, `getAttachmentSize()` |
| `ReturnedMessage`, `ReturnedMessageEnvelope` | `getAttachmentSize()` |
| `OwnerInfo`, `DataBoxResult`, `Response\GetDataBoxActivityStatus` a request DTO s `dbID` | `getDataBoxId()` |
| `DTO\DataMessageEvent` | `getTime()`, `getDescription()` |
| `DTO\Hash` | `getValue()`, `getAlgorithm()` |
| `Response\GetDeliveryInfo` | `getDelivery()` |
| `Response\VerifyMessage` | `getHash()` |
| `Response\GetUserInfoFromLogin` | `getUserInfo()` |
| `Response\ResignISDSDocument` | `getDocument()` |
| `Response\PDZSendInfo` | `isResult()` |
| všechny obálky a doručenky | `$qTimestamp` |

Co s tím ve volajícím kódu:

- Odpovídající **settery nově přijímají `null`**. To je rozšíření, ne zúžení — kód, který
  settery volá, se měnit nemusí.
- **Gettery** ale nově mohou vrátit `null` tam, kde dřív podle typu nemohly. Pokud vám statická
  analýza (PHPStan/Psalm) na těchto místech ohlásí novou chybu, je to reálný stav, který ISDS
  umí poslat. Ošetřete ho podle domény: vynechat záznam ze seznamu, zobrazit placeholder,
  nebo vyhodit vlastní výjimku. **Nepoužívejte plošně `?? ''` ani `assert()`.**
- `Response::getStatus(): ResponseStatus` **zůstává non-nullable** záměrně — je to základní
  kontrakt knihovny a odpověď bez stavu je stejně nepoužitelná.
- Request DTO v `src/DTO/Request/` zůstávají non-nullable záměrně — chybějící povinná hodnota
  je chyba volajícího a má ji odhalit typový systém.

### 5.1 `getPublishOwnId()` vrací objekt

```bash
grep -rn 'getPublishOwnId' --include=*.php .
```

```php
// 5.0
$bool = $envelope->getPublishOwnId();          // ?bool

// 6.0
$publish = $envelope->getPublishOwnId();       // ?DTO\PublishOwnId
$bool = $publish?->getValue();                 // ?bool
$level = $publish?->getIdLevel();              // ?int, nový atribut IdLevel
```

Setter `setPublishOwnId()` dál přijímá i `bool`, takže zapisující kód se měnit nemusí.

## 6. Nové výjimky, které je potřeba ošetřit

```bash
grep -rn 'createMessage(\|uploadAttachment(\|createBigMessage(' --include=*.php .
```

Všechny výjimky knihovny nově implementují marker interface
`TomasKulhanek\CzechDataBox\Exception\CzechDataBoxException`, takže je lze zachytit jedním
`catch (CzechDataBoxException $e)`. Rozšiřte ošetření o tyto nové typy:

| Výjimka | Kdy nastane |
| --- | --- |
| `AttachmentCountOverflow` | `createMessage()` / `createBigMessage()` s více než 100 přílohami, resp. 10 kontejnerovými ZIP/ASiC |
| `DisallowedAttachmentFormat` | přípona přílohy mimo whitelist vyhlášky č. 194/2009 Sb. (`Utils\AllowedAttachmentFormats`, 71 přípon) |
| `FieldLengthOverflow` | příliš dlouhý `dmAnnotation` (255 znaků), `dm*RefNumber` nebo `dm*Ident` (50 znaků) — konstanty `Connector::MAX_ANNOTATION_LENGTH`, `MAX_REF_NUMBER_LENGTH`, `MAX_IDENT_LENGTH` |
| `SoapFault` (potomek `ConnectionException`) | server vrátil SOAP Fault 1.1 nebo 1.2; nese `faultCode` a `faultString` |
| `InvalidEndpointDomain` | `new EndpointProvider('…')` s hodnotou, která není holé jméno hostu |
| `IsdsStatusError` | vyhazuje ho jen volitelný `Utils\StatusGuard`, viz kapitola 9 |

`uploadAttachment()` a `createBigMessage()` navíc vyhazují už existující `MissingRequiredField`,
`MissingMainFile` a `FileSizeOverflow` — ošetřete je stejně jako u `createMessage()`.

### 6.1 Limit velikosti příloh běžné zprávy

Součet velikostí příloh se kontroluje proti `Connector::MAX_MESSAGE_ATTACHMENTS_SIZE`, což je
**20 MiB** (dřív 25 MiB). Do součtu se nově počítají i přílohy `dmXMLContent`. Zprávy, které se
do limitu nevejdou, odesílejte přes VoDZ — `uploadAttachment()` + `createBigMessage()`, limit
`Connector::MAX_BIG_MESSAGE_ATTACHMENTS_SIZE` = 100 MiB.

## 7. Změny chování bez změny signatury

Tahle kapitola nezpůsobí chybu při kompilaci ani ve statické analýze. Projděte ji ručně.

### 7.1 `getPrevious()` z `ConnectionException` a `SystemExclusion` vrací `null`

```bash
grep -rn 'getPrevious()' --include=*.php .
```

HTTP providery už nezřetězují původní výjimku klienta. Ta totiž drží požadavek s hlavičkou
`Authorization`, takže plný výpis řetězce výjimek posílal Basic Auth do ISDS rovnou do logu
nebo do Sentry.

```php
// 5.0 — typický vzorec, který přestane fungovat
$previous = $e->getPrevious();
if ($previous instanceof GuzzleHttp\Exception\RequestException) {
    $status = $previous->getResponse()?->getStatusCode();
}

// 6.0
$status = $e->getCode();               // stavový kód je dál k dispozici
$detail = $e->getMessage();            // "Trida\Puvodni\Vyjimky: původní zpráva"
```

Zpráva má nově tvar `Trida\Puvodni\Vyjimky: původní zpráva` a obsahuje metodu, URI i stavový řádek.

### 7.2 Znění zpráv `MissingRequiredField`

```bash
grep -rn "Missing PEM data\|MissingRequiredField" --include=*.php tests/ test/ 2>/dev/null
```

Providery hlásí názvy polí, ne věty. Místo `The required field 'Missing PEM data' is empty.`
dostanete `'dbID'`, `'loginName'`, `'password'`, `'publicKey'` nebo `'privateKey'`. Chybějící
jméno a heslo se nově rozlišují a chybějící certifikát se rozlišuje od chybějícího privátního
klíče. **Testy opřené o původní znění zprávy je potřeba upravit.**

Zároveň se přitvrdila validace: `SymfonyClientProvider` dřív request odeslal i s prázdnými
pověřeními (`Basic base64("")`, resp. `Basic base64(":")`) a zbytečně čerpal limit neúspěšných
pokusů o přihlášení. Nově oba providery vyhodí `MissingRequiredField` ještě před odesláním.
U typu přihlášení `HOSTED_SPIS` posílá Symfony provider nově `base64("dbID:")` — s dvojtečkou,
dle RFC 7617 — stejně jako Guzzle.

### 7.3 Svazek CA certifikátů

```bash
grep -rn 'cacert.pem\|caCertPath' --include=*.php --include=Dockerfile --include=*.yml .
```

`src/cacert.pem.dist` byl z balíčku odstraněn, ručně udržovaný soubor stárnul mezi vydáními
knihovny. Třetí parametr konstruktoru providerů je nově `?string $caCertPath = null`; při `null`
se použije systémový svazek přes `composer/ca-bundle`. Pokud jste providerům předávali cestu
`vendor/tomas-kulhanek/czech-data-box/src/cacert.pem`, argument vypusťte. Vlastní svazek lze
dál předat stejným parametrem.

Zkontrolujte i `Dockerfile` a CI konfiguraci, jestli soubor nekopírují nebo nestahují.

### 7.4 Deprecated operace `db_access`

`Connector::getOwnerInfoFromLogin()` a `getUserInfoFromLogin()` mají nově atribut
`#[Deprecated]`. Fungují dál, ale přejděte na `getOwnerInfoFromLogin2()` a `getUserInfoFromLogin2()`,
které vracejí rozšířené údaje (`aifoIsds`, `isdsID`, `dbIdOVM`, RUIAN adresa) v DTO
`OwnerInfoExt2` a `UserInfoExt2`.

## 8. Vlastní implementace `ClientProviderInterface`

```bash
grep -rn 'implements ClientProviderInterface' --include=*.php .
```

Pokud si HTTP provider implementujete sami, **musíte** doplnit čtvrtý parametr:

```php
public function sendRequest(
    Account $account,
    ServiceTypeEnum $serviceType,
    string $xmlBody,
    ?int $maxResponseSize = null     // <-- nový
): string;
```

Bez toho skončí autoload třídy fatální chybou
`Declaration of ...::sendRequest() must be compatible with ClientProviderInterface::sendRequest()`.

`null` znamená „použij výchozí limit provideru" (256 MiB). Uvnitř parametr buď respektujte při
čtení těla — ideálně po blocích, `Provider\ResponseSizeLimit` na to má
`rejectAnnouncedSize()` a `collect()` — nebo ho vědomě ignorujte, pokud si velikost odpovědi
hlídáte jinak.

Pokud dědíte z `GuzzleClientProvider` nebo `SymfonyClientProvider`, zkontrolujte i konstruktor:
první dva parametry přijímají rozhraní (`GuzzleHttp\ClientInterface`, `EndpointProviderInterface`)
místo konkrétních tříd a přibyly čtvrtý a pátý parametr `RequestOptionsFactory` a
`ResponseSizeLimit`, oba s výchozí instancí.

## 9. Volitelné novinky, které stojí za zvážení

Nic z toho není povinné a nic z toho nerozbije stávající kód. Zvažte to až po dokončení
mechanické části migrace.

- **`Utils\StatusGuard`** — `StatusGuard::assertOk($response)` vyhodí `IsdsStatusError`
  (s `statusCode`, `statusMessage`, `refNumber`), pokud odpověď nemá stav OK. Obsahuje mapu
  známých kódů ISDS s českým vysvětlením. U `CreateMessage` kontroluje i dílčí stavy
  `dmMultipleStatus`. Nahrazuje ruční kontroly stavu — a je přesnější než porovnání
  s `'0000'`, protože za úspěch bere celou skupinu kódů `00xx` (`ResponseStatus::isOk()`).
- **Nové operace**: `sentMessageEnvelopeDownload()`, `getMessageAuthor2()`, `eraseMessage()`,
  `getListOfErasedMessages()`, `pickUpAsyncResponse()`, `getListForNotifications()`,
  `registerForNotifications()`, `suspMessageReport()`, `dummyOperation()` (keep-alive),
  `getConstants()`, `getDataBoxAddress()`, správa uživatelů schránky (`getDataBoxUsers2()`,
  `addDataBoxUser2()`, `updateDataBoxUser2()`, `deleteDataBoxUser2()`), otevřené adresování
  (`setOpenAddressing()`, `clearOpenAddressing()`), `newAccessData2()` a archivace
  (`archiveIsdsDocument()`).
- **Nové atributy DTO**: `isSuspicious()` (podezřelá zpráva), `isVodz()` a `getAttsNum()`
  (rozpoznání VoDZ v seznamech zpráv, aniž byste je stahovali).
- **`Account::__debugInfo()`** maskuje `password`, `privateKey` a `privateKeyPassPhrase`
  hodnotou `***`, takže `var_dump()`, `print_r()` ani `dd()` heslo neukážou. Pozor:
  `var_export()` a `serialize()` takový háček nemají — účet do logu neposílejte.

## 10. Kontrolní seznam na závěr

Po dokončení migrace ověřte:

1. **Grep na odstraněná jména** — žádné z nich se v aplikaci nesmí vyskytovat:

   ```bash
   grep -rn 'setProduction\|isProduction\|findPersonalDataBox\|confirmDelivery' --include=*.php .
   grep -rn 'IRequest\|IResponse\|IResponseStatus' --include=*.php .
   grep -rn 'TomasKulhanek\\\\Serializer\\\\\|isTemp()' --include=*.php .
   grep -rn 'CzechDataBox\\\\Traits\|PersonalOwnerInfo' --include=*.php .
   grep -rn 'getDataBoxEffectiveOvm\|setRecipientOrgUnit' --include=*.php .
   grep -rn 'cacert.pem' --include=*.php --include=Dockerfile --include=*.yml .
   ```

   Jediné povolené zbytky jsou `findDataBox2` (obsahuje `findDataBox`) a `Response`/`Request`
   jako krátká jména tříd z jiných knihoven.

2. **`composer validate --strict`** a **`composer why tomas-kulhanek/serializer`** — druhý
   příkaz nesmí nic najít, pokud jste balíček nepoužívali jinde.

3. **Statická analýza** (PHPStan / Psalm) na stejné úrovni jako před migrací. Nové chyby
   `Cannot call method ... on ...|null` na getterech z [kapitoly 5](#5-nullabilita-odpovědí-podle-xsd)
   jsou reálné stavy odpovědí ISDS, ne falešné poplachy — ošetřete je, nepotlačujte.

4. **Testovací sada aplikace**. Selhat mohou typicky testy, které:
   - kontrolují znění zprávy `MissingRequiredField` (viz 7.2),
   - čtou `getPrevious()` z `ConnectionException` (viz 7.1),
   - volají `Account::setProduction()` (viz kapitola 2),
   - posílají přílohy mezi 20 a 25 MiB (viz 6.1).

5. **Smoke test proti testovacímu prostředí** `datovka-test.gov.cz`:
   `GuzzleClientProvider::create(EndpointProvider::test())` a jedno volání
   `dummyOperation()` nebo `getOwnerInfoFromLogin2()`.

6. **Soupis míst k rozhodnutí pro člověka** — všechna místa, u kterých jste si nebyli jistí
   (typicky nullability z kapitoly 5 a překlad `getDataBoxEffectiveOvm()` z 3.3).
