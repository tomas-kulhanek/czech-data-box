# PHP knihovna pro komunikaci s Informačním systémem datových schránek (ISDS) Ministerstva vnitra

![DEV branch workflows](https://github.com/tomas-kulhanek/czech-data-box/actions/workflows/main.yml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/tomas-kulhanek/czech-data-box/v/stable)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![Total Downloads](https://poser.pugx.org/tomas-kulhanek/czech-data-box/downloads)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![Monthly Downloads](https://poser.pugx.org/tomas-kulhanek/czech-data-box/d/monthly)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)
[![License](https://poser.pugx.org/tomas-kulhanek/czech-data-box/license)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)


⚠ **POZOR!!** Pokud implementujete napojení na ISDS, je potřeba aby jste se řídili dle [PROVOZNÍHO ŘÁDU](https://info.mojedatovaschranka.cz/info/cs/80.html)⚠
## Instalace

### Composer

Pro instalaci balíčku je nutné jej instalovat skrze [composer](https://getcomposer.org/).

```bash
composer require tomas-kulhanek/czech-data-box
```

Dále je potřeba využít nějakého klienta. Buď je možné využít [Guzzle](https://github.com/guzzle/guzzle/) nebo [Symfony Http client](https://github.com/symfony/http-client)
```bash
composer require tomas-kulhanek/czech-data-box guzzlehttp/guzzle:^7.0
```
```bash
composer require tomas-kulhanek/czech-data-box symfony/http-client
```

V případě využívání vlastního http klienta, stačí implementovat rozhraní `TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface` a předat ho do konstruktoru třídy `TomasKulhanek\CzechDataBox\Connector`. Samozřejmostí je třeba zajistit správné nastavení hlaviček nebo SSL klientských certifikátů.

## Popis
Tato knihovna slouží k základní komunikaci s Informačním systémem datových scrhánek [ISDS](https://mojedatovaschranka.cz) nebo [ISDS test](https://czebox.cz)

## Základní použití
Pro každou operaci je potřebné zadat přístupové údaje

```php
<?php
$account = new \TomasKulhanek\CzechDataBox\Account();
$account->setPassword('mojeTajneHeslo')
        ->setLoginName('mujLogin')
        ->setLoginType(\TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum::NAME_PASSWORD)
        ->setProduction(false);
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

## Využití s Guzzle 7
### Instalace
```bash
composer require tomas-kulhanek/czech-data-box guzzlehttp/guzzle:^7.0
```
#### Instancování 
```php
$serializer = \TomasKulhanek\Serializer\SerializerFactory::create();
$guzzleProvider = \TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider::create();
$connector = new \TomasKulhanek\CzechDataBox\Connector($serializer, $guzzleProvider);
```
## Pomoc a řešní chyb

V případě že potřebujete poradit, nebo při implementaci Vám třída zobrazuje chybu vytvořte prosím nové Issues.
Základní pomoc je poskytována zcela zdarma pomocí Issues.

## Odkazy
- Produkční ISDS - https://mojedatoveschranky.cz
- Testovací ISDS - https://czebox.cz
- Provozní řád ISDS - https://info.mojedatovaschranka.cz/info/cs/80.html
- Poradna - https://poradnaisds.cz/

## Žádosti o zřízení datové schránky
### Produkční prostředí
- orgány veřejné moci - [odkaz](https://www.datoveschranky.info/documents/1744842/1746058/sprava_dalsich_DS_OVM.zfo/cfd889e3-0c11-4228-d87f-5c426dfc5ebb)
- ostatní - [odkaz](https://www.datoveschranky.info/documents/1744842/1746063/zadost_zrizeni_ds.zfo/42ee7c26-16dd-427f-94c8-319453efdae4)

### Testovací prostředí
Zřízení testovací schránky v prostředí czecbox.cz je možné skrze formulář na produkčním portalu www.mojedatoveschranky.cz po přihlášení v nastavení
