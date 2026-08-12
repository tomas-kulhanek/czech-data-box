# Bezpečnostní politika

Knihovna `tomas-kulhanek/czech-data-box` zpracovává **přihlašovací údaje k ISDS** — jméno a heslo, systémové certifikáty i obsah datových zpráv. Chybu, která by mohla vést k jejich úniku nebo zneužití, proto řešíme přednostně.

## Podporované verze

| Verze | Podpora                                                     |
|-------|-------------------------------------------------------------|
| 6.x   | ✅ aktivní vývoj, bezpečnostní opravy                        |
| < 6.0 | ❌ bez podpory — 5.x skončila vydáním 6.0.0 dne 12. 08. 2026 |

Bezpečnostní opravy vycházejí jen pro řadu 6.x. Používáte-li 5.x, přejděte na 6.0 podle návodu [UPGRADE-6.0.md](UPGRADE-6.0.md); zpětné porty do 5.x se nedělají.

## Hlášení zranitelnosti

**Zranitelnost prosím nehlaste veřejným issue ani pull requestem.**

Použijte jednu z těchto cest:

1. **GitHub Security Advisory** (preferováno) — [Report a vulnerability](https://github.com/tomas-kulhanek/czech-data-box/security/advisories/new) v repozitáři [tomas-kulhanek/czech-data-box](https://github.com/tomas-kulhanek/czech-data-box). Hlášení je neveřejné a umožňuje koordinované zveřejnění.
2. **E-mail** — [jsem@tomaskulhanek.cz](mailto:jsem@tomaskulhanek.cz) s předmětem začínajícím `[SECURITY] czech-data-box`.

### Co do hlášení uvést

- verzi knihovny a verzi PHP,
- použitý HTTP provider (`GuzzleClientProvider`, `SymfonyClientProvider`, vlastní),
- popis dopadu (únik pověření, obejití ověření, RCE, DoS, …),
- postup reprodukce, ideálně minimální ukázku kódu nebo test,
- **nikdy neposílejte reálná přihlašovací údaje, certifikáty ani obsah skutečných datových zpráv** — použijte testovací data z prostředí [datovka-test.gov.cz](https://datovka-test.gov.cz).

### Co můžete čekat

| Krok                          | Lhůta                                            |
|-------------------------------|--------------------------------------------------|
| Potvrzení přijetí hlášení     | do 3 pracovních dnů                              |
| Prvotní vyhodnocení a závažnost | do 10 pracovních dnů                            |
| Oprava a vydání               | podle závažnosti, u kritických chyb co nejdříve   |

Po vydání opravy zveřejníme advisory a chybu popíšeme v [CHANGELOG.md](CHANGELOG.md) v sekci `Zabezpečení`. Pokud si nepřejete být uvedeni, dejte prosím vědět; jinak vás v advisory rádi uvedeme jako reportéra.

## Co do působnosti knihovny nepatří

- **Incidenty a výpadky na straně ISDS** — hlaste na [helpdesk Digitální a informační agentury](https://datovka.gov.cz/info/cs/68.html), nikoli sem.
- **Zranitelnosti v závislostech** (Guzzle, Symfony HTTP Client, OpenSSL) — hlaste přímo jejich autorům. Pokud jde o způsob, jakým je knihovna používá, hlášení sem patří.
- **Chybějící ověření dat na straně vaší aplikace** — knihovna validuje jen to, co vyžaduje Provozní řád ISDS.

## Doporučení pro bezpečné použití

- **Nelogujte objekt `Account`.** `Account::__debugInfo()` sice heslo, privátní klíč i passphrase v `var_dump()` a `print_r()` maskuje, `var_export()` ani `serialize()` však takový háček nemají.
- **Nezachytávejte a nepředávejte dál výjimky s pověřeními.** HTTP providery knihovny záměrně nepředávají původní výjimku klienta jako `previous` — nesla by v sobě požadavek s hlavičkou `Authorization`. Pokud si píšete vlastní `ClientProviderInterface`, řiďte se stejným pravidlem.
- **Doména endpointu musí pocházet z důvěryhodné konfigurace.** Na výslednou URL se posílají přihlašovací údaje i klientský certifikát; vlastní doménu v `EndpointProvider` nikdy neskládejte ze vstupu od uživatele.
- **Nastavte `zend.exception_ignore_args=1`** v produkci, pokud stack trace odesíláte do externí služby (Sentry, Monolog). Knihovna kritické parametry označuje atributem `#[\SensitiveParameter]`, ale ne každý kód ve vašem zásobníku to dělá.
- **Ověřujte certifikační autoritu.** Providery ve výchozím stavu používají `composer/ca-bundle`; `verify`/`cafile` nikdy nevypínejte.
