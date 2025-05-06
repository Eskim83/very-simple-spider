<?php

require __DIR__ . '/../vendor/autoload.php';

use VerySimpleSpider\Spider;
use VerySimpleSpider\Support\Logger;
use VerySimpleSpider\Support\Downloader;
use VerySimpleSpider\Support\RobotsTxt;

$url = "https://example.com";
$logger = new Logger(true);
$downloader = new Downloader();
$robots = new RobotsTxt($url);

$spider = new Spider($url, 2, $logger, $downloader);
$spider->setMaxPages(10);
$spider->setUrlFilter(fn($url) => $robots->isAllowed($url));

$spider->setOnPageDownloaded(function ($url, $html) {
    echo "[✓] Got: $url (" . strlen($html) . " bytes)\n";
});

$spider->run();