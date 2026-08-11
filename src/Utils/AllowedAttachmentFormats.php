<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Utils;

/**
 * Povolené formáty příloh datové zprávy dle Přílohy 3 vyhlášky č. 194/2009 Sb.
 * a Provozního řádu ISDS (WS manipulace s datovými zprávami, kap. 3, verze 3.8).
 */
final class AllowedAttachmentFormats
{
    /**
     * @var string[]
     */
    public const array EXTENSIONS = [
        'asice',
        'asics',
        'cer',
        'crt',
        'csv',
        'dbf',
        'ddd',
        'der',
        'dgn',
        'doc',
        'docx',
        'dwg',
        'edi',
        'fo',
        'gfs',
        'gif',
        'gml',
        'heic',
        'heif',
        'htm',
        'html',
        'isdoc',
        'isdocx',
        'jfif',
        'jpeg',
        'jpg',
        'json',
        'm4a',
        'm4p',
        'm4v',
        'mp2',
        'mp3',
        'mp4',
        'mpeg',
        'mpeg1',
        'mpeg2',
        'mpg',
        'odp',
        'ods',
        'odt',
        'p7b',
        'p7c',
        'p7f',
        'p7m',
        'p7s',
        'pdf',
        'pk7',
        'png',
        'ppt',
        'pptx',
        'prj',
        'qix',
        'rtf',
        'sbn',
        'sbx',
        'sce',
        'scs',
        'shp',
        'shx',
        'tif',
        'tiff',
        'tsr',
        'tst',
        'txt',
        'wav',
        'xls',
        'xlsx',
        'xml',
        'xsd',
        'zfo',
        'zip',
    ];

    /**
     * Kontejnerové formáty (ZIP/ASiC) — v jedné zprávě jich smí být nejvýše 10.
     *
     * @var string[]
     */
    public const array CONTAINER_EXTENSIONS = [
        'asice',
        'asics',
        'sce',
        'scs',
        'zip',
    ];

    public static function isAllowed(string $fileName): bool
    {
        return in_array(self::getExtension($fileName), self::EXTENSIONS, true);
    }

    public static function isContainer(string $fileName): bool
    {
        return in_array(self::getExtension($fileName), self::CONTAINER_EXTENSIONS, true);
    }

    private static function getExtension(string $fileName): string
    {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }
}
