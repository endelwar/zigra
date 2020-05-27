<?php

namespace ZigraTest;

use Zigra_Tools;

class Zigra_ToolsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider brProvider
     */
    public function testBr2nl($br)
    {
        $this->assertSame(chr(13) . chr(10), Zigra_Tools::br2nl($br));
    }

    public function brProvider()
    {
        return array(
            array('<br>'),
            array('<br/>'),
            array('<br />'),
            array('<br / >'),
            array('<br      /    >'),
        );
    }
}
