<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Serializer;

use JMS\Serializer\Context;

use function bin2hex;
use function count;
use function random_bytes;
use function sprintf;

/**
 * Sbírá obsahy příloh při serializaci požadavku, aby se nemusely vkládat do XML jako jeden obří řetězec.
 *
 * {@see SplFileInfoHandler} v serializačním kontextu s tímto kolektorem místo base64 obsahu zapíše
 * zástupný token a soubor si tu zaregistruje. Odesílač pak token nahradí obsahem až při zápisu
 * do HTTP požadavku (viz {@see \TomasKulhanek\CzechDataBox\Provider\StreamedRequestBody}).
 *
 * Token je alfanumerický, takže projde XML serializací beze změny, a nese náhodný nonce, aby se nikdy
 * nemohl shodovat s uživatelským textem (anotace, čísla jednací apod.).
 */
final class AttachmentCollector
{
    public const string CONTEXT_ATTRIBUTE = 'czech-data-box.attachment-collector';

    private readonly string $nonce;

    /**
     * @var array<string, SplFileInfo> token => soubor
     */
    private array $files = [];

    public function __construct()
    {
        $this->nonce = bin2hex(random_bytes(8));
    }

    public static function fromContext(Context $context): ?self
    {
        if (!$context->hasAttribute(self::CONTEXT_ATTRIBUTE)) {
            return null;
        }
        $collector = $context->getAttribute(self::CONTEXT_ATTRIBUTE);

        return $collector instanceof self ? $collector : null;
    }

    /**
     * Regulární výraz (bez oddělovačů), který zachytí kterýkoli token tohoto kolektoru.
     */
    public function getTokenPattern(): string
    {
        return 'CzechDataBoxAttachment' . $this->nonce . '_\\d+_';
    }

    public function register(SplFileInfo $file): string
    {
        $token = sprintf('CzechDataBoxAttachment%s_%d_', $this->nonce, count($this->files));
        $this->files[$token] = $file;

        return $token;
    }

    /**
     * @return array<string, SplFileInfo>
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function isEmpty(): bool
    {
        return $this->files === [];
    }
}
