# PHP knihovna pro komunikaci s Informačním systémem datových schránek (ISDS) Ministerstva vnitra

![CRAN](https://img.shields.io/cran/l/devtools.svg)
![PyPI - License](https://img.shields.io/pypi/l/Django.svg)
[![Latest stable](https://img.shields.io/packagist/v/tomas-kulhanek/czech-data-box.svg)](https://packagist.org/packages/tomas-kulhanek/czech-data-box)

⚠ **POZOR!!** Pokud implementujete napojení na ISDS, je potřeba aby jste se řídili dle [PROVOZNÍHO ŘÁDU](https://www.datoveschranky.info/dulezite-informace/provozni-rad-isds)⚠
## Instalace

### Composer

Pro instalaci balíčku je nutné jej instalovat skrze [composer](https://getcomposer.org/).

```bash
composer require tomas-kulhanek/czech-data-box
```

## Popis
Tato knihovna slouží k základní komunikaci s Informačním systémem datových scrhánek [ISDS](https://mojedatovaschranka.cz) nebo [ISDS test](https://czebox.cz)

Veškeré ukázky, jak pracovat s knihovnou naleznete v examples. Jediná podmínka ke zprovoznění je ta, že musíte vlastnit své přístupové údaje.

## Základní použití
Pro každou operaci je potřebné zadat přístupové údaje
```php
<?php
$account = new \TomasKulhanek\CzechDataBox\Connector\Account();
try {
    $account->setPassword('mojeTajneHeslo')
        ->setLoginName('mujLogin')
            ->setLoginType(\TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum::get(\TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum::LOGIN_NAME_PASSWORD))
            ->setPortalType(\TomasKulhanek\CzechDataBox\Enum\PortalTypeEnum::get(\TomasKulhanek\CzechDataBox\Enum\PortalTypeEnum::CZEBOX));
} catch (\TomasKulhanek\CzechDataBox\Exception\BadOptionException $exception) {
    die($exception->getMessage());
}
```
Prostředí ke kterému se připojuje je definováno pomocí ``\TomasKulhanek\CzechDataBox\Connector\Account::setPortalType()``

## Pomoc a řešní chyb

V případě že potřebujete poradit, nebo při implementaci Vám třída zobrazuje chybu vytvořte prosím nové Issues.
Základní pomoc je poskytována zcela zdarma pomocí Issues.

## Odkazy
- Produkční ISDS - https://mojedatoveschranky.cz
- Testovací ISDS - https://czebox.cz
- Software602, a.s. - https://602.cz
- Provozní řád ISDS - https://www.datoveschranky.info/dulezite-informace/provozni-rad
- Oznamované změny - https://www.datoveschranky.info/

## Žádosti o zřízení datové schránky
### Produkční prostředí
- orgány veřejné moci - [odkaz](https://www.datoveschranky.info/documents/1744842/1746058/sprava_dalsich_DS_OVM.zfo/cfd889e3-0c11-4228-d87f-5c426dfc5ebb)
- ostatní - [odkaz](https://www.datoveschranky.info/documents/1744842/1746063/zadost_zrizeni_ds.zfo/42ee7c26-16dd-427f-94c8-319453efdae4)

### Testovací prostředí
- všechny - [okdaz](https://www.datoveschranky.info/documents/1744842/1746073/zadost_zrizeni_testovaci_ds.zfo/4b75d5bf-0272-4305-9cef-8ec8f019e9d3)

## Časté otázky
### Proč CURL a ne SoapClient?
Důvod je jednoduchý. Jelikož PHP nedokázalo správně zpracovávat pomocí ClassMap request/response viz [bug](https://bugs.php.net/bug.php?id=45404). Z toho důvodu, jsme zvolili využití curl a serializeru. Problém byl například v CreateMessage a proto jsme na internetu nikde nenašli knihovnu, která by umožňovala odesílání datových zpráv.
