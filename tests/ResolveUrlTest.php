<?php

use PHPUnit\Framework\TestCase;
use VerySimpleSpider\Spider;
use VerySimpleSpider\Support\Logger;
use VerySimpleSpider\Support\Downloader;

class ResolveUrlTest extends TestCase
{
    protected function getSpider(): Spider
    {
        return new Spider('https://example.com/base/page.html', 1, new Logger(false), new Downloader());
    }

    public function testAbsoluteUrlUnchanged()
    {
        $spider = $this->getSpider();
        $result = $this->invokeResolveUrl($spider, 'https://external.com/foo');
        $this->assertSame('https://external.com/foo', $result);
    }

    public function testSchemelessUrlConverted()
    {
        $spider = $this->getSpider();
        $result = $this->invokeResolveUrl($spider, '//cdn.example.com/resource');
        $this->assertSame('https://cdn.example.com/resource', $result);
    }

    public function testRelativePathResolved()
    {
        $spider = $this->getSpider();
        $result = $this->invokeResolveUrl($spider, 'image.jpg');
        $this->assertSame('https://example.com/base/image.jpg', $result);
    }

    public function testAnchorAppended()
    {
        $spider = $this->getSpider();
        $result = $this->invokeResolveUrl($spider, '#top');
        $this->assertSame('https://example.com/base/page.html#top', $result);
    }

    protected function invokeResolveUrl(Spider $spider, string $rel): string
    {
        $ref = new ReflectionClass($spider);
        $method = $ref->getMethod('resolveUrl');
        $method->setAccessible(true);
        $startUrl = (fn() => $this->startUrl)->call($spider);
		return $method->invoke($spider, $startUrl, $rel);
    }
}
