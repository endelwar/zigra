<?php

namespace ZigraTest;

use PHPUnit\Framework\TestCase;
use Zigra\Version;

class VersionTest extends TestCase
{

    public function testGetVersion()
    {
        self::assertEquals(Version::VERSION, (new Version())->getVersion());
    }
}
