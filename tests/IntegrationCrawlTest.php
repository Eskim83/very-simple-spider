<?php

use PHPUnit\Framework\TestCase;
use VerySimpleSpider\Spider;
use VerySimpleSpider\Support\Logger;
use VerySimpleSpider\Support\Downloader;
use VerySimpleSpider\Support\RobotsTxt;

class IntegrationCrawlTest extends TestCase
{
    public function testCrawlEskimPl()
    {
        $url = 'https://example.com';
        $logger = new Logger(false);
        $downloader = new Downloader();
        $robots = new RobotsTxt($url);

        $spider = new Spider($url, 1, $logger, $downloader);
        $spider->setMaxPages(3);
        $spider->setUrlFilter(fn($url) => $robots->isAllowed($url));

        $visited = [];
        $spider->setOnPageDownloaded(function ($url, $html) use (&$visited) {
            $visited[] = $url;
        });

        $spider->run();

        $this->assertGreaterThanOrEqual(1, count($visited), 'At least one page should be visited');
        $this->assertContains('https://example.com', $visited);
    }
}
