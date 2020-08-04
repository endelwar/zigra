<?php

declare(strict_types=1);

namespace ZigraTest;

use PHPUnit\Framework\TestCase;
use Zigra\Registry;
use Zigra\Registry\Tplvar;

class registryInstanceTest extends TestCase
{
    public function testRegistrySingletonSameness(): void
    {
        $registry1 = Registry::getInstance();
        $registry2 = Tplvar::getInstance();
        self::assertInstanceOf(Registry::class, $registry1);
        self::assertInstanceOf(Tplvar::class, $registry2);
        self::assertNotEquals($registry1, $registry2);
        self::assertNotSame($registry1, $registry2);
    }
}
