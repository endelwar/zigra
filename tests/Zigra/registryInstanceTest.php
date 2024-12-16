<?php

namespace ZigraTest;

class registryInstanceTest extends \PHPUnit\Framework\TestCase
{
    public function testRegistrySingletonSameness()
    {
        $registry1 = \Zigra_Registry::getInstance();
        $registry2 = \Zigra_Registry_Tplvar::getInstance();
        $this->assertInstanceOf(\Zigra_Registry::class, $registry1);
        $this->assertInstanceOf(\Zigra_Registry_Tplvar::class, $registry2);
        $this->assertNotEquals($registry1, $registry2);
        $this->assertNotSame($registry1, $registry2);
    }
}
