<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Utils;

use TomasKulhanek\CzechDataBox\Exception\BadOptionException;

class DataBoxStatus
{
    public const GENERAL = 'GENERAL';
    public const ADDRESS = 'ADDRESS';
    public const ICO = 'ICO';
    public const DBID = 'DBID';
    public const ALL = 'ALL';
    public const OVM = 'OVM';
    public const OVM_MAIN = 'OVM_MAIN';
    public const OVM_REQ = 'OVM_REQ';
    public const OVM_NOTAR = 'OVM_NOTAR';
    public const OVM_EXEKUT = 'OVM_EXEKUT';
    public const OVM_FO = 'OVM_FO';
    public const OVM_PFO = 'OVM_PFO';
    public const OVM_PO = 'OVM_PO';
    public const PO = 'PO';
    public const PO_ZAK = 'PO_ZAK';
    public const PO_REQ = 'PO_REQ';
    public const PFO = 'PFO';
    public const PFO_ADVOK = 'PFO_ADVOK';
    public const PFO_INSSPR = 'PFO_INSSPR';
    public const PFO_DANPOR = 'PFO_DANPOR';
    public const PFO_AUDITOR = 'PFO_AUDITOR';
    public const FO = 'FO';

    public const TYPE_GENERAL = 'GENERAL';
    public const TYPE_ADDRESS = 'ADDRESS';
    public const TYPE_ICO = 'ICO';
    public const TYPE_DBID = 'DBID';
    public const SCOPE_ALL = 'ALL';
    public const SCOPE_OVM = 'OVM';
    public const SCOPE_OVM_MAIN = 'OVM_MAIN';
    public const SCOPE_OVM_REQ = 'OVM_REQ';
    public const SCOPE_OVM_NOTAR = 'OVM_NOTAR';
    public const SCOPE_OVM_EXEKUT = 'OVM_EXEKUT';
    public const SCOPE_OVM_FO = 'OVM_FO';
    public const SCOPE_OVM_PFO = 'OVM_PFO';
    public const SCOPE_OVM_PO = 'OVM_PO';
    public const SCOPE_PO = 'PO';
    public const SCOPE_PO_ZAK = 'PO_ZAK';
    public const SCOPE_PO_REQ = 'PO_REQ';
    public const SCOPE_PFO = 'PFO';
    public const SCOPE_PFO_ADVOK = 'PFO_ADVOK';
    public const SCOPE_PFO_INSSPR = 'PFO_INSSPR';
    public const SCOPE_PFO_DANPOR = 'PFO_DANPOR';
    public const SCOPE_PFO_AUDITOR = 'PFO_AUDITOR';
    public const SCOPE_FO = 'FO';

    public const PDZ_K = 'K';
    public const PDZ_O = 'O';
    public const PDZ_G = 'G';
    public const PDZ_E = 'E';

    /**
     * @return array<string, string>
     */
    public static function getPdzTypes(): array
    {
        return [
            self::PDZ_E => 'PDZ z kreditu',
            self::PDZ_G => 'Glob??ln?? dotovan??',
            self::PDZ_O => 'Odpov??dn?? PDZ',
            self::PDZ_K => 'Smluvn?? PDZ',
        ];
    }

    /**
     * @param string $type
     * @return mixed
     * @throws BadOptionException
     */
    public static function getPdzTypeAsString(string $type)
    {
        if (in_array($type, self::getPDZTypes())) {
            return self::getPDZTypes()[$type];
        }
        throw new BadOptionException(sprintf('The value %s is not allowed', $type));
    }

    /**
     * @return array<int|string, string>
     */
    public static function getDataBoxTypes(): array
    {
        return [
            self::ALL => 'ALL',
            self::OVM => 'Org??n ve??ejn?? moci',
            self::OVM_REQ => 'Schr??nka OVM z????zen?? na ????dost',
            self::OVM_NOTAR => 'Org??n ve??ejn?? moci - not????',
            self::OVM_EXEKUT => 'Org??n ve??ejn?? moci - exekutor',
            self::OVM_FO => 'Org??n ve??ejn?? moci z fyzick?? osoby',
            self::OVM_PFO => 'Org??n ve??ejn?? moci z pr??vnick?? fyzick?? osoby',
            self::OVM_PO => 'Org??n ve??ejn?? moci z pr??vnick?? osoby',
            self::PO => 'Pr??vnick?? osoba zapsan?? v obchodn??m rejst????ku',
            self::PO_ZAK => 'Pr??vnick?? osoba z????zen?? z??konem',
            self::PO_REQ => 'Pr??vnick?? osoba - na ????dost',
            self::PFO => 'Podnikaj??c?? fyzick?? osoba',
            self::PFO_ADVOK => 'Advok??t',
            self::PFO_INSSPR => 'Insolven??n?? spr??vce',
            self::PFO_DANPOR => 'Da??ov?? poradce',
            self::PFO_AUDITOR => 'Statut??rn?? auditor',
            self::FO => 'Fyzick?? osoba',
            null => 'Technick?? skupina',
        ];
    }

    /**
     * @param string $type
     * @return string
     * @throws BadOptionException
     */
    public static function getDataBoxTypeAsString(string $type): string
    {
        if (in_array($type, self::getDataBoxTypes())) {
            return self::getDataBoxTypes()[$type];
        }
        throw new BadOptionException(sprintf('The value %s is not allowed', $type));
    }
}
