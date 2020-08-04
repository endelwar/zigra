<?php

declare(strict_types=1);

namespace ZigraTest;

use PHPUnit\Framework\TestCase;
use Zigra\Tools;

class ToolsTest extends TestCase
{
    /**
     * @dataProvider brProvider
     *
     * @param string $br
     */
    public function testBr2nl($br): void
    {
        self::assertSame(chr(13) . chr(10), Tools::br2nl($br));
    }

    public function brProvider(): array
    {
        return [
            ['<br>'],
            ['<br/>'],
            ['<br />'],
            ['<br / >'],
            ['<br      /    >'],
        ];
    }
}
