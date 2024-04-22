<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Utils;

use TomasKulhanek\CzechDataBox\Exception\BadOptionException;

class MessageStatus
{
    public const FILTER_ALL = -1;
    public const FILTER_SUBMITTED = 1;
    public const FILTER_STAMPED = 2;
    public const FILTER_ANTIVIRUS_FAILED = 3;
    public const FILTER_DELIVERED = 4;
    public const FILTER_DELIVERED_AFTER_TIME = 5;
    public const FILTER_DELIVERED_BY_LOGIN = 6;
    public const FILTER_READ = 7;
    public const FILTER_UNDELIVERED = 8;
    public const FILTER_DELETED = 9;
    public const FILTER_IN_VAULT = 10;

    public static function getDecEntryForStatus(int ...$statuses): float
    {
        if (!is_array($statuses)) {
            $statuses = [$statuses];
        }
        $output = 0;
        foreach ($statuses as $status) {
            if (
                !in_array($status, [
                self::FILTER_ALL,
                self::FILTER_SUBMITTED,
                self::FILTER_STAMPED,
                self::FILTER_ANTIVIRUS_FAILED,
                self::FILTER_DELIVERED,
                self::FILTER_DELIVERED_AFTER_TIME,
                self::FILTER_DELIVERED_BY_LOGIN,
                self::FILTER_READ,
                self::FILTER_UNDELIVERED,
                self::FILTER_DELETED,
                self::FILTER_IN_VAULT,
                ])
            ) {
                throw new BadOptionException(sprintf('The value %s is not allowed. Use one of the %s::FILTER_*', $status, self::class));
            }

            $output += 2 ** $status;
        }

        return $output;
    }

    /**
     * @return array<int,string>
     */
    public static function getStateList(): array
    {
        return [
            0 => 'Nelze zjístit stav zprávy',
            self::FILTER_SUBMITTED => 'Datová zpráva byla podána (vznikla v ISDS).',
            self::FILTER_STAMPED => 'Datová zpráva včetně písemností podepsána podacím časovým razítkem.',
            self::FILTER_ANTIVIRUS_FAILED => 'Datová zpráva neprošla AV kontrolou - zpráva není ani dodána; konečný stav zprávy před smazáním.',
            self::FILTER_DELIVERED => 'Datová zpráva dodána do schránky adresáta (zapsán čas dodání), je přístupná adresátovi.',
            self::FILTER_DELIVERED_AFTER_TIME => 'Uplynulo 10 dní od dodání veřejné zprávy, která dosud nebyla doručena přihlášením (předpoklad doručení fikcí u neOVM schránky); u komerční nebo systémové zprávy nemůže tento stav nastat.',
            self::FILTER_DELIVERED_BY_LOGIN => 'Osoba (nebo aplikace přihlašující se systémovým certifikátem) oprávněná číst tuto zprávu se přihlásila - zpráva byla doručena přihlášením.',
            self::FILTER_READ => 'Zpráva byla přečtena (na portále nebo akcí ESS).',
            self::FILTER_UNDELIVERED => 'Zpráva byla označena jako nedoručitelná, protože schránka adresáta byla zpětně znepřístupněna; netýká se systémových zpráv.',
            self::FILTER_DELETED => 'Obsah zprávy byl smazán, obálka zprávy včetně hashů přesunuta do archivu (jen některé služby umí přistupovat k archivním obálkám zpráv)',
            self::FILTER_IN_VAULT => 'Zpráva byla přesunuta do Datového trezoru odesílatele nebo adresáta (nebo obou); netýká se systémových zpráv.',
        ];
    }

    /**
     * @return string
     * @throws BadOptionException
     */
    public static function getMessageStateAsString(?int $type): string
    {
        if (in_array($type, self::getStateList())) {
            return self::getStateList()[$type];
        }
        throw new BadOptionException(sprintf('The value %s is not allowed', $type));
    }
}
