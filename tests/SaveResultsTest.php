<?php

use PHPUnit\Framework\TestCase;
use VerySimpleSpider\Spider;
use VerySimpleSpider\Support\Logger;
use VerySimpleSpider\Support\Downloader;

class SaveResultsTest extends TestCase
{
    private string $outputFile;

    protected function setUp(): void
    {
        $this->outputFile = __DIR__ . '/output.json';
        if (file_exists($this->outputFile)) {
            unlink($this->outputFile);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->outputFile)) {
            unlink($this->outputFile);
        }
    }

    public function testJsonOutput()
    {
        $url = 'https://example.com';
        $logger = new Logger(false);
        $downloader = $this->createMock(Downloader::class);
        $downloader->method('fetch')->willReturn('<a href="/test">Test</a>');

        $spider = new Spider($url, 1, $logger, $downloader);
        $spider->setMaxPages(2);

        $results = [];

        $spider->setOnPageDownloaded(function ($url, $html) use (&$results) {
            $results[] = ['url' => $url, 'length' => strlen($html)];
        });

        $spider->run();

        file_put_contents($this->outputFile, json_encode($results, JSON_PRETTY_PRINT));
        $this->assertFileExists($this->outputFile);
        $json = json_decode(file_get_contents($this->outputFile), true);
        $this->assertIsArray($json);
        $this->assertNotEmpty($json);
        $this->assertArrayHasKey('url', $json[0]);
    }
}
