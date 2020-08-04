<?php

declare(strict_types=1);

namespace ZigraTest\Registry;

use PHPUnit\Framework\TestCase;
use Zigra\Registry\Tplvar;

class TplvarTest extends TestCase
{
    public function setUp(): void
    {
        Tplvar::clear();
    }

    public function tearDown(): void
    {
        Tplvar::clear();
    }

    public function testRegistryGetInstance(): void
    {
        // getting instance initializes instance
        $registry = Tplvar::getInstance();
        self::assertInstanceOf(Tplvar::class, $registry);
    }

    public function testGet(): void
    {
        Tplvar::set('foo', 'bar');
        $bar = Tplvar::get('foo');
        self::assertEquals('bar', $bar);

        $registryInstance = Tplvar::getInstance();
        self::assertEquals('bar', $registryInstance->foo);
        self::assertEquals('bar', $registryInstance->get('foo'));
    }

    public function testSet(): void
    {
        // setting value initializes instance
        Tplvar::set('foo', 'bar');
        $registry = Tplvar::getInstance();
        self::assertInstanceOf(Tplvar::class, $registry);

        Tplvar::set('myNullValue', null);
        $nullValue = Tplvar::get('myNullValue');
        self::assertNull($nullValue);
    }

    public function testAdd(): void
    {
        Tplvar::set('foo', ['bar']);
        Tplvar::add('foo', 42);
        Tplvar::add('foo', 3.14);

        self::assertEquals(['foo' => ['bar', 42, 3.14]], Tplvar::getAll());
    }

    public function testHas(): void
    {
        Tplvar::set('foo', 'bar');
        self::assertTrue(Tplvar::has('foo'));
        self::assertFalse(Tplvar::has('notfoo'));
    }

    public function testGetAll(): void
    {
        Tplvar::set('foo', 'bar');
        $registryInstance = Tplvar::getInstance();
        $registryInstance->set('foo2', 42);
        $registryInstance->foo3 = 3.14;

        self::assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], Tplvar::getAll());
        self::assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], $registryInstance->getAll());
    }

    public function testGetKeys(): void
    {
        Tplvar::set('foo', 'bar');
        Tplvar::set('foo2', 42);
        Tplvar::set('foo3', 3.14);
        self::assertEquals(['foo', 'foo2', 'foo3'], Tplvar::getKeys());
    }

    public function testRegistrySingletonSameness(): void
    {
        $registry1 = Tplvar::getInstance();
        $registry2 = Tplvar::getInstance();
        self::assertInstanceOf(Tplvar::class, $registry1);
        self::assertInstanceOf(Tplvar::class, $registry2);
        self::assertEquals($registry1, $registry2);
        self::assertSame($registry1, $registry2);
    }
}
