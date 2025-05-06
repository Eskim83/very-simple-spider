<?php

use PHPUnit\Framework\TestCase;
use VerySimpleSpider\Support\Downloader;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class DownloaderTest extends TestCase
{
    public function testFetchReturnsContentFromGuzzle()
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient->method('get')
                   ->willReturn(new Response(200, [], 'mocked content'));

        $downloader = new Downloader($mockClient);
        $result = $downloader->fetch('https://example.com');

        $this->assertSame('mocked content', $result);
    }

    public function testFallbackToFileGetContents()
    {
        $downloader = new Downloader(null); // brak Guzzle, fallback na native

        // używamy adresu, który prawie zawsze działa
        $content = $downloader->fetch('https://example.com');

        $this->assertNotNull($content);
        $this->assertStringContainsStringIgnoringCase('example', $content);
    }
}
