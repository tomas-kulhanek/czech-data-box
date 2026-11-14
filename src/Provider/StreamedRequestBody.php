<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use Generator;
use IteratorAggregate;
use Stringable;
use TomasKulhanek\CzechDataBox\Exception\FileSystemException;
use TomasKulhanek\CzechDataBox\Serializer\AttachmentCollector;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

use function array_key_exists;
use function base64_encode;
use function clearstatcache;
use function count;
use function fclose;
use function filesize;
use function fopen;
use function fread;
use function intdiv;
use function is_int;
use function is_string;
use function min;
use function preg_split;
use function sprintf;
use function strlen;
use function substr;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Tělo SOAP požadavku složené z hotového XML a příloh, které se do něj base64 kódují až při čtení.
 *
 * Do paměti se vejde jen XML bez příloh a jeden blok právě čteného souboru; velikost těla
 * (`Content-Length`) je známá předem, protože base64 délku určuje jednoznačně (4 znaky na 3 bajty).
 * Objekt je opakovaně průchozí: každé `getIterator()` začíná odznovu, takže HTTP klient může
 * požadavek při opakování znovu přehrát.
 *
 * @implements IteratorAggregate<int, string>
 */
final readonly class StreamedRequestBody implements IteratorAggregate, Stringable
{
    /**
     * Násobek tří, aby každý blok kromě posledního dal base64 bez zarovnávacích `=`.
     */
    private const int RAW_CHUNK_SIZE = 3 * 21_845;

    /**
     * @param list<string|array{file: SplFileInfo, size: int}> $parts u souboru velikost z okamžiku výpočtu
     *        `Content-Length`, aby šla změna souboru před odesláním odhalit
     */
    private function __construct(
        private array $parts,
        private int $contentLength
    ) {
    }

    /**
     * @param string $xml serializované XML s tokeny přiložených souborů z $collector
     * @throws FileSystemException soubor nejde přečíst nebo se token v XML nenašel právě jednou
     */
    public static function create(string $xml, AttachmentCollector $collector): self
    {
        $files = $collector->getFiles();
        if ($files === []) {
            return new self([$xml], strlen($xml));
        }

        $pieces = preg_split(
            '/(' . $collector->getTokenPattern() . ')/',
            $xml,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        if ($pieces === false) {
            throw new FileSystemException('The request XML could not be split into attachments.');
        }

        $parts = [];
        $used = [];
        $length = 0;
        foreach ($pieces as $piece) {
            if (array_key_exists($piece, $files)) {
                if (isset($used[$piece])) {
                    throw new FileSystemException(
                        sprintf('The attachment placeholder %s appears more than once in the request.', $piece)
                    );
                }
                $used[$piece] = true;
                $size = self::sizeOf($files[$piece]);
                $parts[] = ['file' => $files[$piece], 'size' => $size];
                $length += self::encodedLength($size);
                continue;
            }
            $parts[] = $piece;
            $length += strlen($piece);
        }
        if (count($used) !== count($files)) {
            throw new FileSystemException('An attachment placeholder is missing in the serialized request.');
        }

        return new self($parts, $length);
    }

    /**
     * Přesná délka těla v bajtech tak, jak bude odesláno.
     */
    public function getContentLength(): int
    {
        return $this->contentLength;
    }

    /**
     * @return Generator<int, string>
     * @throws FileSystemException soubor se mezi výpočtem velikosti a odesláním změnil
     */
    public function getIterator(): Generator
    {
        foreach ($this->parts as $part) {
            if (is_string($part)) {
                yield $part;
                continue;
            }
            yield from self::encodeFile($part['file'], $part['size']);
        }
    }

    /**
     * Celé tělo najednou. Slouží jako záloha tam, kde HTTP klient stream neumí, a pro testy;
     * paměť zde roste s velikostí příloh, takže ho při odesílání nepoužívejte.
     */
    public function __toString(): string
    {
        $body = '';
        foreach ($this as $chunk) {
            $body .= $chunk;
        }

        return $body;
    }

    private static function encodedLength(int $rawSize): int
    {
        return 4 * intdiv($rawSize + 2, 3);
    }

    private static function sizeOf(SplFileInfo $file): int
    {
        $path = $file->getPathname();
        clearstatcache(true, $path);
        $size = @filesize($path);
        if (!is_int($size)) {
            throw new FileSystemException(sprintf('Cannot determine the size of the attachment %s.', $path));
        }

        return $size;
    }

    /**
     * @return Generator<int, string>
     */
    private static function encodeFile(SplFileInfo $file, int $expected): Generator
    {
        $path = $file->getPathname();
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            throw new FileSystemException(sprintf('Cannot open the attachment %s for reading.', $path));
        }

        try {
            $carry = '';
            $read = 0;
            while (($remaining = $expected - $read) > 0) {
                $chunk = fread($handle, min(self::RAW_CHUNK_SIZE, $remaining));
                if ($chunk === false || $chunk === '') {
                    break;
                }
                $read += strlen($chunk);
                $carry .= $chunk;
                $usable = strlen($carry) - strlen($carry) % 3;
                if ($usable > 0) {
                    yield base64_encode(substr($carry, 0, $usable));
                    $carry = substr($carry, $usable);
                }
            }
            if ($read !== $expected || fread($handle, 1) !== '') {
                throw new FileSystemException(
                    sprintf('The attachment %s changed while the request was being sent.', $path)
                );
            }
            if ($carry !== '') {
                yield base64_encode($carry);
            }
        } finally {
            fclose($handle);
        }
    }
}
