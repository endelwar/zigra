<?php

namespace ZigraTest;

class Zigra_RegistryTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        \Zigra_Registry::clear();
    }

    protected function tearDown(): void
    {
        \Zigra_Registry::clear();
    }

    public function testRegistryGetInstance()
    {
        // getting instance initializes instance
        $registry = \Zigra_Registry::getInstance();
        $this->assertInstanceOf(\Zigra_Registry::class, $registry);
    }

    public function testGet()
    {
        \Zigra_Registry::set('foo', 'bar');
        $bar = \Zigra_Registry::get('foo');
        $this->assertEquals('bar', $bar);

        $registryInstance = \Zigra_Registry::getInstance();
        $this->assertEquals('bar', $registryInstance->foo);
        $this->assertEquals('bar', $registryInstance->get('foo'));
    }

    public function testSet()
    {
        // setting value initializes instance
        \Zigra_Registry::set('foo', 'bar');
        $registry = \Zigra_Registry::getInstance();
        $this->assertInstanceOf(\Zigra_Registry::class, $registry);

        \Zigra_Registry::set('myNullValue', null);
        $nullValue = \Zigra_Registry::get('myNullValue');
        $this->assertNull($nullValue);
    }

    public function testAdd()
    {
        \Zigra_Registry::set('foo', ['bar']);
        \Zigra_Registry::add('foo', 42);
        \Zigra_Registry::add('foo', 3.14);

        $this->assertEquals(['foo' => ['bar', 42, 3.14]], \Zigra_Registry::getAll());
    }

    public function testHas()
    {
        \Zigra_Registry::set('foo', 'bar');
        $this->assertTrue(\Zigra_Registry::has('foo'));
        $this->assertFalse(\Zigra_Registry::has('notfoo'));
    }

    public function testGetAll()
    {
        \Zigra_Registry::set('foo', 'bar');
        $registryInstance = \Zigra_Registry::getInstance();
        $registryInstance->set('foo2', 42);
        $registryInstance->foo3 = 3.14;

        $this->assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], \Zigra_Registry::getAll());
        $this->assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], $registryInstance->getAll());
    }

    public function testGetKeys()
    {
        \Zigra_Registry::set('foo', 'bar');
        \Zigra_Registry::set('foo2', 42);
        \Zigra_Registry::set('foo3', 3.14);
        $this->assertEquals(['foo', 'foo2', 'foo3'], \Zigra_Registry::getKeys());
    }

    public function testRegistrySingletonSameness()
    {
        $registry1 = \Zigra_Registry::getInstance();
        $registry2 = \Zigra_Registry::getInstance();
        $this->assertInstanceOf(\Zigra_Registry::class, $registry1);
        $this->assertInstanceOf(\Zigra_Registry::class, $registry2);
        $this->assertEquals($registry1, $registry2);
        $this->assertSame($registry1, $registry2);
    }
}
