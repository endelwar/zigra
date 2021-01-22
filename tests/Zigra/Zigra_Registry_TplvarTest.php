<?php

namespace ZigraTest;

use Zigra_Registry_Tplvar;

class Zigra_Registry_TplvarTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        Zigra_Registry_Tplvar::clear();
    }

    public function tearDown(): void
    {
        Zigra_Registry_Tplvar::clear();
    }

    public function testRegistryGetInstance()
    {
        // getting instance initializes instance
        $registry = Zigra_Registry_Tplvar::getInstance();
        $this->assertInstanceOf(Zigra_Registry_Tplvar::class, $registry);
    }

    public function testGet()
    {
        Zigra_Registry_Tplvar::set('foo', 'bar');
        $bar = Zigra_Registry_Tplvar::get('foo');
        $this->assertEquals('bar', $bar);

        $registryInstance = Zigra_Registry_Tplvar::getInstance();
        $this->assertEquals('bar', $registryInstance->foo);
        $this->assertEquals('bar', $registryInstance->get('foo'));
    }

    public function testSet()
    {
        // setting value initializes instance
        Zigra_Registry_Tplvar::set('foo', 'bar');
        $registry = Zigra_Registry_Tplvar::getInstance();
        $this->assertInstanceOf(Zigra_Registry_Tplvar::class, $registry);

        Zigra_Registry_Tplvar::set('myNullValue', null);
        $nullValue = Zigra_Registry_Tplvar::get('myNullValue');
        $this->assertNull($nullValue);
    }

    public function testAdd()
    {
        Zigra_Registry_Tplvar::set('foo', ['bar']);
        Zigra_Registry_Tplvar::add('foo', 42);
        Zigra_Registry_Tplvar::add('foo', 3.14);

        $this->assertEquals(['foo' => ['bar', 42, 3.14]], Zigra_Registry_Tplvar::getAll());
    }

    public function testHas()
    {
        Zigra_Registry_Tplvar::set('foo', 'bar');
        $this->assertTrue(Zigra_Registry_Tplvar::has('foo'));
        $this->assertFalse(Zigra_Registry_Tplvar::has('notfoo'));
    }

    public function testGetAll()
    {
        Zigra_Registry_Tplvar::set('foo', 'bar');
        $registryInstance = Zigra_Registry_Tplvar::getInstance();
        $registryInstance->set('foo2', 42);
        $registryInstance->foo3 = 3.14;

        $this->assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], Zigra_Registry_Tplvar::getAll());
        $this->assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], $registryInstance->getAll());
    }

    public function testGetKeys()
    {
        Zigra_Registry_Tplvar::set('foo', 'bar');
        Zigra_Registry_Tplvar::set('foo2', 42);
        Zigra_Registry_Tplvar::set('foo3', 3.14);
        $this->assertEquals(['foo', 'foo2', 'foo3'], Zigra_Registry_Tplvar::getKeys());
    }

    public function testRegistrySingletonSameness()
    {
        $registry1 = Zigra_Registry_Tplvar::getInstance();
        $registry2 = Zigra_Registry_Tplvar::getInstance();
        $this->assertInstanceOf(Zigra_Registry_Tplvar::class, $registry1);
        $this->assertInstanceOf(Zigra_Registry_Tplvar::class, $registry2);
        $this->assertEquals($registry1, $registry2);
        $this->assertSame($registry1, $registry2);
    }
}
