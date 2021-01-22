<?php

namespace ZigraTest;

use Zigra_Tools;

class Zigra_ToolsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider brProvider
     * @param string $br
     */
    public function testBr2nl($br)
    {
        $this->assertSame(chr(13) . chr(10), Zigra_Tools::br2nl($br));
    }

    public function brProvider()
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
