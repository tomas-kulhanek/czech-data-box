<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Utils;

use TomasKulhanek\CzechDataBox\Exception\BadOptionException;

class DataBoxStatus
{
	final public const GENERAL = 'GENERAL';
	final public const ADDRESS = 'ADDRESS';
	final public const ICO = 'ICO';
	final public const DBID = 'DBID';
	final public const ALL = 'ALL';
	final public const OVM = 'OVM';
	final public const OVM_MAIN = 'OVM_MAIN';
	final public const OVM_REQ = 'OVM_REQ';
	final public const OVM_NOTAR = 'OVM_NOTAR';
	final public const OVM_EXEKUT = 'OVM_EXEKUT';
	final public const OVM_FO = 'OVM_FO';
	final public const OVM_PFO = 'OVM_PFO';
	final public const OVM_PO = 'OVM_PO';
	final public const PO = 'PO';
	final public const PO_ZAK = 'PO_ZAK';
	final public const PO_REQ = 'PO_REQ';
	final public const PFO = 'PFO';
	final public const PFO_ADVOK = 'PFO_ADVOK';
	final public const PFO_INSSPR = 'PFO_INSSPR';
	final public const PFO_DANPOR = 'PFO_DANPOR';
	final public const PFO_AUDITOR = 'PFO_AUDITOR';
	final public const FO = 'FO';

	final public const TYPE_GENERAL = 'GENERAL';
	final public const TYPE_ADDRESS = 'ADDRESS';
	final public const TYPE_ICO = 'ICO';
	final public const TYPE_DBID = 'DBID';
	final public const SCOPE_ALL = 'ALL';
	final public const SCOPE_OVM = 'OVM';
	final public const SCOPE_OVM_MAIN = 'OVM_MAIN';
	final public const SCOPE_OVM_REQ = 'OVM_REQ';
	final public const SCOPE_OVM_NOTAR = 'OVM_NOTAR';
	final public const SCOPE_OVM_EXEKUT = 'OVM_EXEKUT';
	final public const SCOPE_OVM_FO = 'OVM_FO';
	final public const SCOPE_OVM_PFO = 'OVM_PFO';
	final public const SCOPE_OVM_PO = 'OVM_PO';
	final public const SCOPE_PO = 'PO';
	final public const SCOPE_PO_ZAK = 'PO_ZAK';
	final public const SCOPE_PO_REQ = 'PO_REQ';
	final public const SCOPE_PFO = 'PFO';
	final public const SCOPE_PFO_ADVOK = 'PFO_ADVOK';
	final public const SCOPE_PFO_INSSPR = 'PFO_INSSPR';
	final public const SCOPE_PFO_DANPOR = 'PFO_DANPOR';
	final public const SCOPE_PFO_AUDITOR = 'PFO_AUDITOR';
	final public const SCOPE_FO = 'FO';

	final public const PDZ_K = 'K';
	final public const PDZ_O = 'O';
	final public const PDZ_G = 'G';
	final public const PDZ_E = 'E';

	/**
	 * @return array<string, string>
	 */
	public static function getPdzTypes(): array
	{
		return [
			self::PDZ_E => 'PDZ z kreditu',
			self::PDZ_G => 'Globálně dotovaná',
			self::PDZ_O => 'Odpovědní PDZ',
			self::PDZ_K => 'Smluvní PDZ',
		];
	}

	/**
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
			self::OVM => 'Orgán veřejné moci',
			self::OVM_REQ => 'Schránka OVM zřízená na žádost',
			self::OVM_NOTAR => 'Orgán veřejné moci - notář',
			self::OVM_EXEKUT => 'Orgán veřejné moci - exekutor',
			self::OVM_FO => 'Orgán veřejné moci z fyzické osoby',
			self::OVM_PFO => 'Orgán veřejné moci z právnické fyzické osoby',
			self::OVM_PO => 'Orgán veřejné moci z právnické osoby',
			self::PO => 'Právnická osoba zapsaná v obchodním rejstříku',
			self::PO_ZAK => 'Právnická osoba zřízená zákonem',
			self::PO_REQ => 'Právnická osoba - na žádost',
			self::PFO => 'Podnikající fyzická osoba',
			self::PFO_ADVOK => 'Advokát',
			self::PFO_INSSPR => 'Insolvenční správce',
			self::PFO_DANPOR => 'Daňový poradce',
			self::PFO_AUDITOR => 'Statutární auditor',
			self::FO => 'Fyzická osoba',
			null => 'Technická skupina',
		];
	}

	/**
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
