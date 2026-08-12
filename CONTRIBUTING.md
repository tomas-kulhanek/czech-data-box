# Jak přispívat

Díky, že chcete knihovnu vylepšit. Příspěvky jsou vítané — od hlášení chyb přes doplnění
dokumentace až po nové operace ISDS.

## Hlášení chyb a návrhy

Chyby a náměty hlaste přes [Issues](https://github.com/tomas-kulhanek/czech-data-box/issues).
U chyby prosím uveďte verzi knihovny a PHP, volanou operaci ISDS a (bez citlivých údajů)
odeslaný požadavek či odpověď.

> [!WARNING]
> Do issue ani do pull requestu nikdy nevkládejte přihlašovací údaje k datové schránce,
> obsah datových zpráv ani certifikáty. Lokální soubory `.data/`, `.env` a `composer.lock`
> jsou proto v `.gitignore`.

## Vývojové prostředí

Knihovna vyžaduje **PHP 8.4 nebo novější** a rozšíření `curl`, `dom`, `mbstring`, `openssl` a `xml`.

```bash
git clone https://github.com/tomas-kulhanek/czech-data-box.git
cd czech-data-box
composer install
```

Kdo nechce instalovat PHP lokálně, může použít přiložený `Dockerfile`
(`docker build --build-arg PHP_VERSION=8.4 .`).

## Brány kvality

Před odesláním pull requestu musí projít stejná sada kontrol, jakou spouští CI
(workflow `.github/workflows/main.yml`):

```bash
composer check
```

Skript je zkratkou za jednotlivé kontroly, které lze pouštět i samostatně:

| Příkaz                   | Co dělá                                            |
|--------------------------|----------------------------------------------------|
| `composer check:phpstan` | statická analýza PHPStan na levelu `max`            |
| `composer check:cs`      | kontrola stylu `phpcs` proti PSR-12                 |
| `composer check:rector`  | Rector v režimu `--dry-run` (sada pravidel PHP 8.4) |
| `composer test:unit`     | unit testy (PHPUnit, testsuite `unit`)              |

Automaticky opravitelné nálezy vyřeší `composer fix:all` (Rector + `phpcbf`).

PHPStan běží na nejvyšším levelu. Chybu prosím řešte opravou příčiny, ne potlačením přes
`@phpstan-ignore` nebo `ignoreErrors`.

### Testy

- **Unit testy** (`tests/Unit/`) běží offline, HTTP klient je vždy nahrazen mockem
  (`ClientProviderInterface`). Nové chování doplňujte právě sem.
- **Integrační testy** (`tests/Integration/`) volají skutečné testovací prostředí ISDS
  (`datovka-test.gov.cz`) a spouští je jen naplánované workflow `integration.yml`.
  Bez přihlašovacích údajů (`*_LOGIN_USER`) a bez certifikátu v `.data/cert.pem` se celá
  suite přeskočí:

  ```bash
  composer test:integration
  ```

- Testy ve skupině `memory` jsou z výchozího běhu vyloučené, spustí je `--group memory`.
- Serializace požadavků se validuje proti XSD schématům z přílohy Provozního řádu
  (`tests/_data/xsd/`). Když přidáváte nové request DTO, `RequestXsdValidationTest`
  vyžaduje i jeho pokrytí.

## Konvence commitů

Používáme [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/):

```
<typ>(<rozsah>): <stručný popis>

<volitelné tělo — proč, ne jak>
```

Typy používané v tomto repozitáři: `feat`, `fix`, `refactor`, `perf`, `test`, `docs`, `chore`, `ci`.
Rozsah je nepovinný (např. `connector`, `provider`, `dto`, `deps`).

Zpětně nekompatibilní změnu označte vykřičníkem za typem (`refactor!: …`) a popište ji
v těle commitu i v changelogu.

Příklady z historie:

```
fix: serialize into the canonical http:// ISDS namespace (#56)
feat: add StatusGuard for typed ISDS status errors (#58)
chore(deps): require PHP 8.4 and upgrade the dev toolchain (#53)
```

## Jazyk

- **Kód, identifikátory, komentáře a commit messages anglicky.**
- **Changelog a dokumentace (README, tento soubor) česky** — cílovou skupinou knihovny jsou
  čeští vývojáři.
- V nově psaném kódu držte styl okolního souboru.

## Changelog

Každá uživatelsky viditelná změna patří do [CHANGELOG.md](CHANGELOG.md), do sekce dosud
nevydané verze. Formát vychází z [Keep a Changelog](https://keepachangelog.com/cs/1.1.0/),
verzování dle [SemVer](https://semver.org/lang/cs/). Zpětně nekompatibilní změny patří do
podsekce `⚠️ Breaking changes` a musí být popsané tak, aby podle nich šlo migrovat.

## Pull request

1. Práci veďte ve vlastní větvi (`fix/…`, `feat/…`, `chore/…`), ne přímo v `main`.
2. Jeden pull request = jedna logická změna. Nezávislé úpravy dělte do samostatných commitů,
   ať jde případný problémový bod snadno vyjmout.
3. Vyplňte šablonu pull requestu a doplňte changelog.
4. Zkontrolujte, že `composer check` prochází lokálně — CI spouští totéž na PHP 8.4 i 8.5.

## Podklady

- Provozní řád ISDS a jeho přílohy (XSD, WSDL) — <https://datovka.gov.cz/info/cs/80.html>
- Změny webových služeb pro dodavatele — <https://datovka.gov.cz/info/cs/74.html>
- Poradna ISDS — <https://poradnaisds.cz/>
