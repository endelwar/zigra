<?php

declare(strict_types=1);

namespace ZigraTest;

use PHPUnit\Framework\TestCase;
use Zigra\Registry;

class RegistryTest extends TestCase
{
    public function setUp(): void
    {
        Registry::clear();
    }

    public function tearDown(): void
    {
        Registry::clear();
    }

    public function testRegistryGetInstance(): void
    {
        // getting instance initializes instance
        $registry = Registry::getInstance();
        self::assertInstanceOf(Registry::class, $registry);
    }

    public function testGet(): void
    {
        Registry::set('foo', 'bar');
        $bar = Registry::get('foo');
        self::assertEquals('bar', $bar);

        $registryInstance = Registry::getInstance();
        self::assertEquals('bar', $registryInstance->foo);
        self::assertEquals('bar', $registryInstance->get('foo'));
    }

    public function testSet(): void
    {
        // setting value initializes instance
        Registry::set('foo', 'bar');
        $registry = Registry::getInstance();
        self::assertInstanceOf(Registry::class, $registry);

        Registry::set('myNullValue', null);
        $nullValue = Registry::get('myNullValue');
        self::assertNull($nullValue);
    }

    public function testAdd(): void
    {
        Registry::set('foo', ['bar']);
        Registry::add('foo', 42);
        Registry::add('foo', 3.14);

        self::assertEquals(['foo' => ['bar', 42, 3.14]], Registry::getAll());
    }

    public function testHas(): void
    {
        Registry::set('foo', 'bar');
        self::assertTrue(Registry::has('foo'));
        self::assertFalse(Registry::has('notfoo'));
    }

    public function testGetAll(): void
    {
        Registry::set('foo', 'bar');
        $registryInstance = Registry::getInstance();
        $registryInstance->set('foo2', 42);
        $registryInstance->foo3 = 3.14;

        self::assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], Registry::getAll());
        self::assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], $registryInstance->getAll());
    }

    public function testGetKeys(): void
    {
        Registry::set('foo', 'bar');
        Registry::set('foo2', 42);
        Registry::set('foo3', 3.14);
        self::assertEquals(['foo', 'foo2', 'foo3'], Registry::getKeys());
    }

    public function testRegistrySingletonSameness(): void
    {
        $registry1 = Registry::getInstance();
        $registry2 = Registry::getInstance();
        self::assertInstanceOf(Registry::class, $registry1);
        self::assertInstanceOf(Registry::class, $registry2);
        self::assertEquals($registry1, $registry2);
        self::assertSame($registry1, $registry2);
    }

    public function testRegistrySingletonCloning()
    {
        $registry1 = Registry::getInstance();
        $this->expectException(\Error::class);
        $registry2 = clone $registry1;
    }
}
