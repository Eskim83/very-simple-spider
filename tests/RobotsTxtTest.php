<?php

use PHPUnit\Framework\TestCase;
use VerySimpleSpider\Support\RobotsTxt;

class RobotsTxtTest extends TestCase
{
    public function testDisallowedPathReturnsFalse()
    {
        $mock = $this->getMockBuilder(RobotsTxt::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['load'])
            ->getMock();

        $ref = new ReflectionClass($mock);
        $prop = $ref->getProperty('disallowRules');
        $prop->setAccessible(true);
        $prop->setValue($mock, ['/private']);

        $this->assertFalse($mock->isAllowed('https://example.com/private/file.html'));
    }

    public function testAllowedPathReturnsTrue()
    {
        $mock = $this->getMockBuilder(RobotsTxt::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['load'])
            ->getMock();

        $ref = new ReflectionClass($mock);
        $prop = $ref->getProperty('disallowRules');
        $prop->setAccessible(true);
        $prop->setValue($mock, ['/private']);

        $this->assertTrue($mock->isAllowed('https://example.com/public/page.html'));
    }
}
