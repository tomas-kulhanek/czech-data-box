<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;
use TomasKulhanek\CzechDataBox\DTO\Request\UploadAttachment;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

/**
 * Na rozdíl od {@see VodzMemoryTest} (3× velikost přílohy) streamovaný odesílač drží v paměti jen blok souboru.
 */
class StreamedUploadMemoryTest extends TestCase
{
    use SerializerTrait;

    private const int CHUNK_SIZE = 1024 * 1024;

    private const int ATTACHMENT_SIZE = 64 * self::CHUNK_SIZE;

    private const string SOAP_RESPONSE = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
  <soap:Body>
    <p:UploadAttachmentResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmAttID>ATT123456</p:dmAttID>
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:UploadAttachmentResponse>
  </soap:Body>
</soap:Envelope>
XML;

    #[Group('memory')]
    #[RunInSeparateProcess]
    public function testGuzzleUploadPeakMemoryDoesNotGrowWithTheAttachment(): void
    {
        $received = 0;
        $handler = new MockHandler([static function (RequestInterface $request) use (&$received): Response {
            RequestBodyDrain::psr7($request->getBody(), static function (string $chunk) use (&$received): void {
                $received += strlen($chunk);
            });

            return new Response(200, [], self::SOAP_RESPONSE);
        }]);
        $provider = new GuzzleClientProvider(
            new Client(['handler' => HandlerStack::create($handler)]),
            EndpointProvider::test()
        );

        $this->assertBoundedUpload($provider, $received);
    }

    #[Group('memory')]
    #[RunInSeparateProcess]
    public function testSymfonyUploadPeakMemoryDoesNotGrowWithTheAttachment(): void
    {
        $received = 0;
        $client = new MockHttpClient(
            static function (string $method, string $url, array $options) use (&$received): MockResponse {
                RequestBodyDrain::symfony($options['body'] ?? null, static function (string $chunk) use (&$received): void {
                    $received += strlen($chunk);
                });

                return new MockResponse(self::SOAP_RESPONSE);
            }
        );
        $provider = new SymfonyClientProvider($client, EndpointProvider::test());

        $this->assertBoundedUpload($provider, $received);
    }

    private function assertBoundedUpload(ClientProviderInterface $provider, int &$received): void
    {
        $path = tempnam(sys_get_temp_dir(), 'vodz_stream');
        self::assertNotFalse($path);

        try {
            $handle = fopen($path, 'wb');
            self::assertNotFalse($handle);
            $chunk = str_repeat('a', self::CHUNK_SIZE);
            for ($written = 0; $written < self::ATTACHMENT_SIZE; $written += self::CHUNK_SIZE) {
                fwrite($handle, $chunk);
            }
            fclose($handle);
            unset($chunk);

            $account = new Account();
            $account->setLoginName('login')
                ->setPassword('password')
                ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

            $request = new UploadAttachment();
            $request->setFile(
                new BigAttachment()
                    ->setMimeType('application/pdf')
                    ->setDescription('priloha.pdf')
                    ->setEncodedContent(new SplFileInfo($path))
            );

            $response = new Connector(self::createSerializer(), $provider)->uploadAttachment($account, $request);

            self::assertTrue($response->getStatus()->isOk());
            self::assertGreaterThan(self::ATTACHMENT_SIZE * 4 / 3, $received, 'The whole base64 body must be sent.');
            self::assertLessThan(
                self::ATTACHMENT_SIZE / 2,
                memory_get_peak_usage(true),
                'Streaming an attachment must not need memory proportional to its size.'
            );
        } finally {
            @unlink($path);
        }
    }
}
