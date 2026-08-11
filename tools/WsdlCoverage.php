<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Tools;

use Deprecated;
use DOMDocument;
use DOMNodeList;
use DOMXPath;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use TomasKulhanek\CzechDataBox\Connector;

/**
 * Builds the coverage matrix of the ISDS WSDL operations against the public API of {@see Connector}.
 *
 * Operations are read from the WSDL fixtures in tests/_data/wsdl/ (a verbatim copy of appendix 2 of the
 * ISDS operating rules) and matched against the public methods of the connector by reflection. Every
 * operation ends up in exactly one of three buckets:
 *
 *  - supported   ... a connector method implements the operation,
 *  - dropped     ... ISDS itself deprecated the operation and the library deliberately implements only
 *                    the newer variant (see {@see self::successorOf()} and self::DEPRECATED_WITHOUT_SUCCESSOR),
 *  - unsupported ... a real gap in the library.
 *
 * The rendered matrix lives between the marker comments in README.md; --check verifies that the committed
 * README still matches the code, so the matrix cannot silently rot.
 *
 * @phpstan-type CoverageRow array{operation: string, status: string, method: string|null, note: string|null}
 * @phpstan-type CoverageSection array{file: string, description: string, rows: list<CoverageRow>}
 */
final readonly class WsdlCoverage
{
    public const string STATUS_SUPPORTED = 'supported';

    public const string STATUS_DROPPED = 'dropped';

    public const string STATUS_UNSUPPORTED = 'unsupported';

    public const string README_START = '<!-- wsdl-coverage:start -->';

    public const string README_END = '<!-- wsdl-coverage:end -->';

    private const string WSDL_NAMESPACE = 'http://schemas.xmlsoap.org/wsdl/';

    /**
     * Reading order of the WSDL files plus their Czech captions. Files missing from this list are
     * appended (sorted) with an empty caption, so a newly published WSDL still shows up in the matrix.
     *
     * @var array<string, string>
     */
    private const array WSDL_DESCRIPTIONS = [
        'db_access.wsdl' => 'služby související s přístupem do ISDS',
        'db_search.wsdl' => 'vyhledávání datových schránek',
        'db_manipulations.wsdl' => 'manipulace s datovou schránkou a její uživatelé',
        'dm_operations.wsdl' => 'odesílání a stahování datových zpráv',
        'dm_info.wsdl' => 'informace o datových zprávách',
        'dm_VoDZ.wsdl' => 'velkoobjemové datové zprávy (VoDZ, do 100 MB)',
        'dm_arch.wsdl' => 'archivace (přerazítkování) ZFO',
    ];

    /**
     * Operation -> connector method for the pairs that cannot be derived from the name.
     *
     * Everything else is matched case-insensitively, which already covers the acronym casing used by
     * ISDS (ArchiveISDSDocument -> archiveIsdsDocument, ISDSSearch3 -> isdsSearch3, PDZInfo -> pdzInfo).
     * The optional second item is a Czech note rendered in the matrix.
     *
     * @var array<string, array{0: string, 1: string|null}>
     */
    private const array OPERATION_METHOD_MAP = [
        // The library always sends the multi-recipient envelope; the WSDL request of CreateMessage is a
        // subset of CreateMultipleMessage, so a single connector method covers both operations.
        'CreateMessage' => ['createMessage', 'knihovna posílá obálku `CreateMultipleMessage`, která pokrývá i jednoho příjemce'],
        'CreateMultipleMessage' => ['createMessage', 'hromadné odeslání (více příjemců v jednom volání)'],
        // A hyphen cannot appear in a PHP method name.
        'Re-signISDSDocument' => ['resignIsdsDocument', null],
        // Historical naming: the response DTO is GetPasswordInfo, the method says what it returns.
        'GetPasswordInfo' => ['getPasswordExpirationInfo', null],
    ];

    /**
     * Operations that ISDS deprecated without publishing a numbered successor in the same WSDL, so the
     * successor heuristic cannot recognise them.
     *
     * @var array<string, string>
     */
    private const array DEPRECATED_WITHOUT_SUCCESSOR = [
        'FindPersonalDataBox' => 'zrušeno v ISDS 2018, nahrazeno `FindDataBox2`',
    ];

    /**
     * Operations whose connector method existed in 5.x and was removed as a documented BC break of 6.0.0.
     *
     * @var list<string>
     */
    private const array REMOVED_IN_SIX = [
        'FindDataBox',
        'FindPersonalDataBox',
    ];

    /**
     * Czech one-liners describing what the not yet implemented operations do, so the matrix reads as a
     * backlog instead of a bare list of names. Purely cosmetic — an operation missing from this list is
     * still reported as a gap.
     *
     * @var array<string, string>
     */
    private const array UNSUPPORTED_NOTES = [
        'CreateDataBox2' => 'zřízení datové schránky (jen pro OVM s příslušnou rolí)',
        'DeleteDataBox2' => 'znepřístupnění datové schránky',
        'UpdateDataBoxDescr2' => 'změna popisných údajů schránky',
        'NewAccessData2' => 'vygenerování nových přístupových údajů uživatele',
        'DisableDataBoxExternally2' => 'znepřístupnění cizí schránky (agenda OVM)',
        'DisableOwnDataBox2' => 'znepřístupnění vlastní schránky',
        'EnableOwnDataBox2' => 'zpřístupnění vlastní schránky',
        'SetOpenAddressing' => 'zapnutí příjmu poštovních datových zpráv',
        'ClearOpenAddressing' => 'vypnutí příjmu poštovních datových zpráv',
    ];

    /**
     * ISDS services the library deliberately does not implement. They are not part of the WSDL fixtures
     * either, so they are rendered from this list instead of being derived from files.
     *
     * @var array<string, string>
     */
    private const array OUT_OF_SCOPE = [
        '`ChangePassword.wsdl` (služba `asws`)' =>
            'změna hesla přes SMS kód / OTP (`SendSMSCode`, `ChangePasswordOTP`) běží na samostatné službě '
            . '`asws` s vlastní autentizací. Knihovna podporuje běžnou změnu hesla operací `ChangeISDSPassword` '
            . 'z `db_access.wsdl`.',
        '`SetConcept.wsdl`' =>
            'zakládání konceptů zpráv (`SetConcept`, `SetMultipleConcept`) je určené pro předání rozepsané '
            . 'zprávy do webového Portálu datových schránek, ne pro strojové odesílání. Knihovna zprávy '
            . 'odesílá přímo přes `dm_operations.wsdl`.',
        '`ExtWs.wsdl` — odesílací brána (OB)' =>
            'odesílací brána (`extWsLogout`, `GetCredential`) je samostatný produkt ISDS s vlastním modelem '
            . 'autentizace a smluvním režimem. Knihovna cílí na přímé napojení aplikace na ISDS.',
    ];

    public function __construct(
        private string $wsdlDirectory,
        private string $readmePath
    ) {
    }

    /**
     * Renders the whole matrix as the Markdown block stored in README.md.
     */
    public function render(): string
    {
        $sections = $this->buildSections();

        $lines = [];
        $lines[] = 'Matici generuje `php tools/wsdl-coverage.php` z WSDL v [`tests/_data/wsdl/`](tests/_data/wsdl)';
        $lines[] = '(příloha 2 Provozního řádu, verze 3.11) a z reflexe třídy';
        $lines[] = '`TomasKulhanek\CzechDataBox\Connector`. Soulad matice se skutečností hlídá CI';
        $lines[] = '(`composer check:wsdl-coverage`), matici proto needitujte ručně.';
        $lines[] = '';
        $lines[] = 'Legenda: ✅ implementováno · ⛔ záměrně vynecháno (operaci nahradila novější varianta) ·';
        $lines[] = '❌ neimplementováno (skutečná mezera).';
        $lines[] = '';
        $lines[] = '### Souhrn';
        $lines[] = '';
        $lines[] = '| WSDL | Operací | ✅ | ⛔ | ❌ |';
        $lines[] = '| --- | ---: | ---: | ---: | ---: |';

        $totalOperations = 0;
        $totalSupported = 0;
        $totalDropped = 0;
        $totalUnsupported = 0;
        foreach ($sections as $section) {
            $counts = $this->countStatuses($section['rows']);
            $totalOperations += count($section['rows']);
            $totalSupported += $counts['supported'];
            $totalDropped += $counts['dropped'];
            $totalUnsupported += $counts['unsupported'];
            $lines[] = sprintf(
                '| [`%s`](#%s) | %d | %d | %d | %d |',
                $section['file'],
                $this->anchor($section['file']),
                count($section['rows']),
                $counts['supported'],
                $counts['dropped'],
                $counts['unsupported']
            );
        }
        $lines[] = sprintf(
            '| **Celkem** | **%d** | **%d** | **%d** | **%d** |',
            $totalOperations,
            $totalSupported,
            $totalDropped,
            $totalUnsupported
        );

        foreach ($sections as $section) {
            $lines[] = '';
            // The heading holds nothing but the file name, so the anchors built by self::anchor() hold.
            $lines[] = sprintf('### `%s`', $section['file']);
            $lines[] = '';
            if ($section['description'] !== '') {
                $lines[] = sprintf('*%s*', $section['description']);
                $lines[] = '';
            }
            $lines[] = '| Operace | Metoda `Connector` | Stav | Poznámka |';
            $lines[] = '| --- | --- | --- | --- |';
            foreach ($section['rows'] as $row) {
                $lines[] = sprintf(
                    '| `%s` | %s | %s | %s |',
                    $row['operation'],
                    $row['method'] === null ? '—' : sprintf('`%s()`', $row['method']),
                    $this->statusLabel($row['status']),
                    $row['note'] ?? '—'
                );
            }
        }

        $lines[] = '';
        $lines[] = '### Mimo záběr knihovny';
        $lines[] = '';
        $lines[] = 'Následující WSDL přílohy 2 knihovna vědomě neimplementuje, nejde tedy o mezery v pokrytí:';
        $lines[] = '';
        foreach (self::OUT_OF_SCOPE as $service => $reason) {
            $lines[] = sprintf('- **%s** — %s', $service, $reason);
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @return list<string> human readable differences; an empty list means the README is in sync
     */
    public function check(): array
    {
        $expected = trim($this->render());
        $actual = trim($this->currentReadmeBlock());
        if ($expected === $actual) {
            return [];
        }

        $expectedLines = explode("\n", $expected);
        $actualLines = explode("\n", $actual);
        $differences = [];
        $lineCount = max(count($expectedLines), count($actualLines));
        for ($index = 0; $index < $lineCount; $index++) {
            $expectedLine = $expectedLines[$index] ?? '';
            $actualLine = $actualLines[$index] ?? '';
            if ($expectedLine === $actualLine) {
                continue;
            }
            $differences[] = sprintf('řádek %d:', $index + 1);
            $differences[] = sprintf('  README:    %s', $actualLine);
            $differences[] = sprintf('  vygenerováno: %s', $expectedLine);
        }

        return $differences;
    }

    /**
     * Replaces the matrix block in README.md. Returns true when the file actually changed.
     */
    public function write(): bool
    {
        $readme = $this->readFile($this->readmePath);
        $updated = $this->replaceReadmeBlock($readme, "\n" . $this->render());
        if ($updated === $readme) {
            return false;
        }
        if (file_put_contents($this->readmePath, $updated) === false) {
            throw new RuntimeException(sprintf('Soubor "%s" se nepodařilo zapsat.', $this->readmePath));
        }

        return true;
    }

    /**
     * @return list<CoverageSection>
     */
    private function buildSections(): array
    {
        $methods = $this->connectorMethods();
        $sections = [];
        foreach ($this->collectOperations() as $file => $operations) {
            $rows = [];
            foreach ($operations as $operation) {
                $rows[] = $this->classify($operation, $operations, $methods);
            }
            $sections[] = [
                'file' => $file,
                'description' => self::WSDL_DESCRIPTIONS[$file] ?? '',
                'rows' => $rows,
            ];
        }

        return $sections;
    }

    /**
     * @return array<string, list<string>> operations indexed by the WSDL file name
     */
    private function collectOperations(): array
    {
        $files = glob($this->wsdlDirectory . '/*.wsdl');
        if ($files === false || $files === []) {
            throw new RuntimeException(sprintf('V adresáři "%s" není žádné WSDL.', $this->wsdlDirectory));
        }
        sort($files);

        $found = [];
        foreach ($files as $file) {
            $found[basename($file)] = $this->readOperations($file);
        }

        $ordered = [];
        foreach (array_keys(self::WSDL_DESCRIPTIONS) as $known) {
            if (isset($found[$known])) {
                $ordered[$known] = $found[$known];
                unset($found[$known]);
            }
        }

        return $ordered + $found;
    }

    /**
     * Reads the operation names of a single WSDL.
     *
     * The portType is the contract, but the official dm_operations.wsdl declares SignedSentMessageDownload
     * and DummyOperation only in the binding, so both are read and merged in that order.
     *
     * @return list<string>
     */
    private function readOperations(string $file): array
    {
        $content = $this->readFile($file);
        // The published WSDL files start with a UTF-8 BOM.
        $content = (string) preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $document = new DOMDocument();
        $previousState = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($content);
        libxml_clear_errors();
        libxml_use_internal_errors($previousState);
        if (!$loaded) {
            throw new RuntimeException(sprintf('Soubor "%s" není platné XML.', $file));
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('wsdl', self::WSDL_NAMESPACE);

        $operations = [];
        foreach (['//wsdl:portType/wsdl:operation/@name', '//wsdl:binding/wsdl:operation/@name'] as $query) {
            $nodes = $xpath->query($query);
            if (!$nodes instanceof DOMNodeList) {
                continue;
            }
            foreach ($nodes as $node) {
                $name = $node->nodeValue;
                if ($name === null || $name === '' || in_array($name, $operations, true)) {
                    continue;
                }
                $operations[] = $name;
            }
        }
        if ($operations === []) {
            throw new RuntimeException(sprintf('V souboru "%s" není žádná operace.', $file));
        }

        return $operations;
    }

    /**
     * @return array<string, ReflectionMethod> public connector methods indexed by their lowercased name
     */
    private function connectorMethods(): array
    {
        $methods = [];
        foreach (new ReflectionClass(Connector::class)->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || $method->isStatic()) {
                continue;
            }
            $methods[strtolower($method->getName())] = $method;
        }

        return $methods;
    }

    /**
     * @param list<string> $wsdlOperations all operations of the same WSDL
     * @param array<string, ReflectionMethod> $methods
     * @return CoverageRow
     */
    private function classify(string $operation, array $wsdlOperations, array $methods): array
    {
        [$candidate, $note] = self::OPERATION_METHOD_MAP[$operation] ?? [$operation, null];
        $method = $methods[strtolower($candidate)] ?? null;
        if ($method instanceof ReflectionMethod) {
            if ($method->getAttributes(Deprecated::class) !== []) {
                $note = $this->joinNotes($note, 'v knihovně označeno `#[Deprecated]`');
            }

            return [
                'operation' => $operation,
                'status' => self::STATUS_SUPPORTED,
                'method' => $method->getName(),
                'note' => $note,
            ];
        }

        $reason = self::DEPRECATED_WITHOUT_SUCCESSOR[$operation] ?? null;
        if ($reason === null) {
            $successor = $this->successorOf($operation);
            if (in_array($successor, $wsdlOperations, true)) {
                $reason = sprintf('starší varianta, ISDS ji nahradilo operací `%s`', $successor);
            }
        }
        if ($reason !== null) {
            if (in_array($operation, self::REMOVED_IN_SIX, true)) {
                $reason = $this->joinNotes($reason, 'z API odstraněno v 6.0.0, viz [CHANGELOG.md](CHANGELOG.md)');
            }

            return [
                'operation' => $operation,
                'status' => self::STATUS_DROPPED,
                'method' => null,
                'note' => $reason,
            ];
        }

        return [
            'operation' => $operation,
            'status' => self::STATUS_UNSUPPORTED,
            'method' => null,
            'note' => self::UNSUPPORTED_NOTES[$operation] ?? $note,
        ];
    }

    /**
     * ISDS versions its operations by a numeric suffix (FindDataBox -> FindDataBox2, ISDSSearch2 ->
     * ISDSSearch3). An operation is considered superseded when its successor lives in the same WSDL.
     */
    private function successorOf(string $operation): string
    {
        $base = rtrim($operation, '0123456789');
        $suffix = substr($operation, strlen($base));
        $version = $suffix === '' ? 1 : (int) $suffix;

        return $base . ($version + 1);
    }

    private function joinNotes(?string $first, string $second): string
    {
        return $first === null ? $second : $first . '; ' . $second;
    }

    /**
     * @param list<CoverageRow> $rows
     * @return array{supported: int, dropped: int, unsupported: int}
     */
    private function countStatuses(array $rows): array
    {
        $supported = 0;
        $dropped = 0;
        foreach ($rows as $row) {
            if ($row['status'] === self::STATUS_SUPPORTED) {
                $supported++;
            } elseif ($row['status'] === self::STATUS_DROPPED) {
                $dropped++;
            }
        }

        return [
            'supported' => $supported,
            'dropped' => $dropped,
            'unsupported' => count($rows) - $supported - $dropped,
        ];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_SUPPORTED => '✅',
            self::STATUS_DROPPED => '⛔',
            default => '❌',
        };
    }

    /**
     * GitHub builds a heading anchor by lowercasing the text and dropping everything that is not a letter,
     * a digit, a space, a hyphen or an underscore. The per-WSDL headings contain only "`<file>`", so
     * stripping the backticks and the dot is enough.
     */
    private function anchor(string $file): string
    {
        return str_replace('.', '', strtolower($file));
    }

    private function currentReadmeBlock(): string
    {
        $readme = $this->readFile($this->readmePath);
        $start = strpos($readme, self::README_START);
        $end = strpos($readme, self::README_END);
        if ($start === false || $end === false || $end < $start) {
            throw new RuntimeException(
                sprintf(
                    'V souboru "%s" chybí značky %s a %s.',
                    $this->readmePath,
                    self::README_START,
                    self::README_END
                )
            );
        }
        $from = $start + strlen(self::README_START);

        return substr($readme, $from, $end - $from);
    }

    private function replaceReadmeBlock(string $readme, string $block): string
    {
        $start = strpos($readme, self::README_START);
        $end = strpos($readme, self::README_END);
        if ($start === false || $end === false || $end < $start) {
            throw new RuntimeException(
                sprintf(
                    'V souboru "%s" chybí značky %s a %s.',
                    $this->readmePath,
                    self::README_START,
                    self::README_END
                )
            );
        }
        $from = $start + strlen(self::README_START);

        return substr($readme, 0, $from) . $block . substr($readme, $end);
    }

    private function readFile(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException(sprintf('Soubor "%s" se nepodařilo přečíst.', $path));
        }

        return $content;
    }
}
