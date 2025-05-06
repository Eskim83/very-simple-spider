#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use VerySimpleSpider\Spider;
use VerySimpleSpider\Support\Logger;
use VerySimpleSpider\Support\Downloader;
use VerySimpleSpider\Support\RobotsTxt;

$options = getopt("", ["url:", "depth::", "silent", "out::", "limit::", "max-bytes::"]);

if (!isset($options['url'])) {
    echo "Usage: crawl.php --url=URL [--depth=N] [--silent] [--out=filename.json] [--limit=N] [--max-bytes=N]\n";
    exit(1);
}

$url = $options['url'];
$depth = isset($options['depth']) ? (int)$options['depth'] : 3;
$verbose = !isset($options['silent']);
$outFile = $options['out'] ?? null;
$maxPages = isset($options['limit']) ? (int)$options['limit'] : null;
$maxBytes = isset($options['max-bytes']) ? (int)$options['max-bytes'] : null;

$logger = new Logger($verbose);
$downloader = new Downloader();
$robots = new RobotsTxt($url);
$spider = new Spider($url, $depth, $logger, $downloader);

if ($maxPages !== null) {
    $spider->setMaxPages($maxPages);
}

$totalBytes = 0;
$results = [];
$spider->setOnPageDownloaded(function ($url, $html) use (&$results, &$totalBytes, $logger, $maxBytes) {
    $size = strlen($html);
    $totalBytes += $size;
    $logger->log("[+] Downloaded: $url ({$size} bytes), total: {$totalBytes} bytes");

    if ($maxBytes !== null && $totalBytes >= $maxBytes) {
        $logger->log("[!] Max byte limit reached ({$maxBytes} bytes), stopping...");
        exit(0);
    }

    $results[] = [
        'url' => $url,
        'length' => $size,
    ];
});

$spider->setUrlFilter(function ($url) use ($robots) {
    return $robots->isAllowed($url);
});

$spider->run();

if ($outFile) {
    file_put_contents($outFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $logger->log("[✓] Results written to $outFile");
}
