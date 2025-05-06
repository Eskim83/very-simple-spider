<?php

use PHPUnit\Framework\TestCase;
use VerySimpleSpider\Spider;
use VerySimpleSpider\Support\Logger;
use VerySimpleSpider\Support\Downloader;

class SpiderTest extends TestCase
{
    public function testSetMaxPagesLimitsCrawl()
    {
        $logger = new Logger(false);
        $downloader = $this->createMock(Downloader::class);
        $downloader->method('fetch')->willReturn('<a href="/next">link</a>');

        $spider = new Spider('https://example.com', 3, $logger, $downloader);
        $spider->setMaxPages(1);

        $counter = 0;
        $spider->setOnPageDownloaded(function () use (&$counter) {
            $counter++;
        });

        $spider->run();

        $this->assertSame(1, $counter);
    }

    public function testSetIgnoredExtensionsSkipsBinaryLinks()
    {
        $logger = new Logger(false);
        $downloader = $this->createMock(Downloader::class);
        $downloader->method('fetch')->willReturn('<a href="/file.pdf">PDF</a>');

        $spider = new Spider('https://example.com', 3, $logger, $downloader);
        $spider->setMaxPages(2);
        $spider->setIgnoredExtensions(['pdf']);

        $counter = 0;
        $spider->setOnPageDownloaded(function () use (&$counter) {
            $counter++;
        });

        $spider->run();

        $this->assertSame(1, $counter); // tylko pierwszy URL
    }
}
