<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Tools;

use CurlHandle;
use RuntimeException;

/**
 * Kontroluje odkazy v Markdown dokumentaci — lokální soubory, kotvy na nadpisy a externí URL.
 *
 * @phpstan-type Link array{file: string, line: int, target: string}
 */
final readonly class MarkdownLinks
{
    /**
     * Stavové kódy, které bereme jako živý odkaz. 401 a 403 vracejí weby, které odmítají
     * roboty; adresa přitom existuje, takže je nehlásíme jako chybu.
     *
     * @var list<int>
     */
    private const array ACCEPTED_STATUSES = [200, 401, 403];

    private const int REQUEST_TIMEOUT = 20;

    private const string USER_AGENT = 'czech-data-box link checker (+https://github.com/tomas-kulhanek/czech-data-box)';

    public function __construct(private string $rootDirectory)
    {
    }

    /**
     * @return list<string> seznam nálezů, prázdné pole znamená, že je vše v pořádku
     */
    public function checkLocal(): array
    {
        $anchors = [];
        foreach ($this->documents() as $document) {
            $anchors[$document] = $this->anchorsOf($document);
        }

        $problems = [];
        foreach ($this->links() as $link) {
            $target = $link['target'];
            if (str_starts_with($target, 'http')) {
                continue;
            }

            if (str_starts_with($target, '#')) {
                $problems[] = $this->checkAnchor($link, $link['file'], substr($target, 1), $anchors);
                continue;
            }

            [$path, $fragment] = $this->splitFragment($target);
            $absolutePath = $this->rootDirectory . '/' . $path;
            if (!file_exists($absolutePath)) {
                $problems[] = sprintf('%s:%d cíl "%s" neexistuje', $link['file'], $link['line'], $target);
                continue;
            }

            if ($fragment !== null && array_key_exists($path, $anchors)) {
                $problems[] = $this->checkAnchor($link, $path, $fragment, $anchors);
            }
        }

        return array_values(array_filter($problems, static fn(?string $problem): bool => $problem !== null));
    }

    /**
     * @return list<string> seznam nálezů, prázdné pole znamená, že je vše v pořádku
     */
    public function checkExternal(): array
    {
        $problems = [];
        foreach ($this->externalTargets() as $url => $occurrences) {
            $status = $this->fetchStatus($url);
            if ($status !== null && in_array($status, self::ACCEPTED_STATUSES, true)) {
                continue;
            }

            $problems[] = sprintf(
                '%s — %s (%s)',
                implode(', ', $occurrences),
                $url,
                $status === null ? 'spojení selhalo' : sprintf('HTTP %d', $status)
            );
        }

        return $problems;
    }

    /**
     * Odkazy seskupené podle URL, aby se stejná adresa nestahovala opakovaně.
     *
     * @return array<string, list<string>>
     */
    private function externalTargets(): array
    {
        $targets = [];
        foreach ($this->links() as $link) {
            if (!str_starts_with($link['target'], 'http')) {
                continue;
            }

            $targets[$link['target']][] = sprintf('%s:%d', $link['file'], $link['line']);
        }

        return $targets;
    }

    /**
     * @param Link $link
     * @param array<string, list<string>> $anchors
     */
    private function checkAnchor(array $link, string $document, string $fragment, array $anchors): ?string
    {
        if (in_array($fragment, $anchors[$document] ?? [], true)) {
            return null;
        }

        return sprintf(
            '%s:%d kotva "#%s" v souboru "%s" neexistuje',
            $link['file'],
            $link['line'],
            $fragment,
            $document
        );
    }

    /**
     * @return array{0: string, 1: string|null}
     */
    private function splitFragment(string $target): array
    {
        $position = strpos($target, '#');
        if ($position === false) {
            return [$target, null];
        }

        return [substr($target, 0, $position), substr($target, $position + 1)];
    }

    /**
     * @return list<Link>
     */
    private function links(): array
    {
        $links = [];
        foreach ($this->documents() as $document) {
            $lines = explode("\n", $this->stripCode($this->read($document)));
            foreach ($lines as $index => $line) {
                preg_match_all('/\[[^]]*]\(([^)\s]+)\)/', $line, $matches);
                foreach ($matches[1] as $target) {
                    if (str_starts_with($target, 'mailto:')) {
                        continue;
                    }

                    $links[] = ['file' => $document, 'line' => $index + 1, 'target' => $target];
                }
            }
        }

        return $links;
    }

    /**
     * Kotvy, které GitHub vygeneruje z nadpisů daného souboru.
     *
     * @return list<string>
     */
    private function anchorsOf(string $document): array
    {
        preg_match_all('/^#{1,6}\s+(.*)$/m', $this->stripFences($this->read($document)), $matches);

        $anchors = [];
        $seen = [];
        foreach ($matches[1] as $heading) {
            $anchor = $this->slug($heading);
            $occurrence = $seen[$anchor] ?? 0;
            $seen[$anchor] = $occurrence + 1;
            $anchors[] = $occurrence === 0 ? $anchor : sprintf('%s-%d', $anchor, $occurrence);
        }

        return $anchors;
    }

    /**
     * Napodobuje způsob, jakým GitHub tvoří kotvy: malá písmena, pryč interpunkce,
     * mezery na spojovníky. Diakritika zůstává.
     */
    private function slug(string $heading): string
    {
        $text = (string) preg_replace('/\[([^]]*)]\([^)]*\)/', '$1', trim($heading));
        $text = str_replace(['`', '*'], '', $text);
        $text = mb_strtolower($text);
        $text = (string) preg_replace('/[^\p{L}\p{N}\s_-]+/u', '', $text);

        return (string) preg_replace('/\s/u', '-', $text);
    }

    /**
     * Odstraní blokový i řádkový kód, aby se URL v ukázkách nepovažovaly za odkazy.
     */
    private function stripCode(string $contents): string
    {
        return (string) preg_replace('/`[^`]*`/', '', $this->stripFences($contents));
    }

    /**
     * Vyprázdní řádky uvnitř ohraničených bloků kódu. Nadpisy si díky tomu podrží
     * zpětné apostrofy, které GitHub do kotvy nepočítá, ale text v nich ano.
     */
    private function stripFences(string $contents): string
    {
        $lines = explode("\n", $contents);
        $inFence = false;
        foreach ($lines as $index => $line) {
            if (str_starts_with(ltrim($line), '```')) {
                $inFence = !$inFence;
                $lines[$index] = '';
                continue;
            }

            if ($inFence) {
                $lines[$index] = '';
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Markdown soubory v kořeni repozitáře a v adresáři `.github`, relativně ke kořeni.
     *
     * @return list<string>
     */
    private function documents(): array
    {
        $found = array_merge(
            glob($this->rootDirectory . '/*.md') ?: [],
            glob($this->rootDirectory . '/.github/*.md') ?: []
        );

        $documents = [];
        foreach ($found as $path) {
            $documents[] = ltrim(str_replace($this->rootDirectory, '', $path), '/');
        }

        sort($documents);

        return $documents;
    }

    private function read(string $document): string
    {
        $contents = file_get_contents($this->rootDirectory . '/' . $document);
        if ($contents === false) {
            throw new RuntimeException(sprintf('Soubor "%s" nelze přečíst.', $document));
        }

        return $contents;
    }

    private function fetchStatus(string $url): ?int
    {
        $status = $this->request($url, true);
        if ($status === null || $status === 405 || $status === 404) {
            // Část serverů na HEAD neodpovídá správně, zkusíme ještě GET.
            $status = $this->request($url, false) ?? $status;
        }

        return $status;
    }

    private function request(string $url, bool $headOnly): ?int
    {
        $handle = curl_init($url);
        if (!$handle instanceof CurlHandle) {
            return null;
        }

        curl_setopt_array($handle, [
            CURLOPT_NOBODY => $headOnly,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::REQUEST_TIMEOUT,
            CURLOPT_USERAGENT => self::USER_AGENT,
        ]);
        curl_exec($handle);
        $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        return $status === 0 ? null : $status;
    }
}
