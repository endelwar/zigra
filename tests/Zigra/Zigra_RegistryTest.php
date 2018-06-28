<?php

namespace ZigraTest;

use Zigra_Registry;

class Zigra_RegistryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Zigra_Registry::clear();
    }

    public function tearDown()
    {
        Zigra_Registry::clear();
    }

    public function testRegistryGetInstance()
    {
        // getting instance initializes instance
        $registry = Zigra_Registry::getInstance();
        $this->assertInstanceOf(Zigra_Registry::class, $registry);
    }

    public function testGet()
    {
        Zigra_Registry::set('foo', 'bar');
        $bar = Zigra_Registry::get('foo');
        $this->assertEquals('bar', $bar);

        $registryInstance = Zigra_Registry::getInstance();
        $this->assertEquals('bar', $registryInstance->foo);
        $this->assertEquals('bar', $registryInstance->get('foo'));
    }

    public function testSet()
    {
        // setting value initializes instance
        Zigra_Registry::set('foo', 'bar');
        $registry = Zigra_Registry::getInstance();
        $this->assertInstanceOf(Zigra_Registry::class, $registry);

        Zigra_Registry::set('myNullValue', null);
        $nullValue = Zigra_Registry::get('myNullValue');
        $this->assertEquals(null, $nullValue);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetWithoutSecondArgoument()
    {
        Zigra_Registry::set('myNullValue');
    }

    public function testAdd()
    {
        Zigra_Registry::set('foo', ['bar']);
        Zigra_Registry::add('foo', 42);
        Zigra_Registry::add('foo', 3.14);

        $this->assertEquals(['foo' => ['bar', 42, 3.14]], Zigra_Registry::getAll());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddWithoutSecondArgoument()
    {
        Zigra_Registry::add('noSecondArg');
    }

    public function testHas()
    {
        Zigra_Registry::set('foo', 'bar');
        $this->assertTrue(Zigra_Registry::has('foo'));
        $this->assertFalse(Zigra_Registry::has('notfoo'));
    }

    public function testGetAll()
    {
        Zigra_Registry::set('foo', 'bar');
        $registryInstance = Zigra_Registry::getInstance();
        $registryInstance->set('foo2', 42);
        $registryInstance->foo3 = 3.14;

        $this->assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], Zigra_Registry::getAll());
        $this->assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], $registryInstance->getAll());
    }

    public function testGetKeys()
    {
        Zigra_Registry::set('foo', 'bar');
        Zigra_Registry::set('foo2', 42);
        Zigra_Registry::set('foo3', 3.14);
        $this->assertEquals(['foo', 'foo2', 'foo3',], Zigra_Registry::getKeys());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotExistStatictMethod()
    {
        Zigra_Registry::notExists('foo', 'bar');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotExistMethod()
    {
        $registryInstance = Zigra_Registry::getInstance();
        $registryInstance->notExists('foo', 'bar');
    }

    public function testRegistrySingletonSameness()
    {
        $registry1 = Zigra_Registry::getInstance();
        $registry2 = Zigra_Registry::getInstance();
        $this->assertInstanceOf(Zigra_Registry::class, $registry1);
        $this->assertInstanceOf(Zigra_Registry::class, $registry2);
        $this->assertEquals($registry1, $registry2);
        $this->assertSame($registry1, $registry2);
    }

}
