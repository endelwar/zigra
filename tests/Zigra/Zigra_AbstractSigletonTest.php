<?php

use PHPUnit\Framework\TestCase;

class Zigra_AbstractSigletonTest extends TestCase
{
    public function testGetInstanceReturnsSameInstance(): void
    {
        $instance1 = TestSingleton::getInstance();
        $instance2 = TestSingleton::getInstance();

        $this->assertSame($instance1, $instance2, 'getInstance() should return the same instance');
    }

    public function testCloneIsNotAllowed(): void
    {
        $this->expectException(Error::class);

        $instance = TestSingleton::getInstance();
        $clonedInstance = clone $instance; // Tentativo di clonazione
    }

    public function testPrivateConstructorIsNotAccessible(): void
    {
        $reflection = new ReflectionClass(Zigra_AbstractSigleton::class);
        $constructor = $reflection->getConstructor();

        $this->assertTrue($constructor->isPrivate(), 'Constructor must be private to prevent direct instantiation');
    }
}

/**
 * Concrete implementation of Zigra_AbstractSigleton for testing.
 */
class TestSingleton extends Zigra_AbstractSigleton
{
    // Test class to extend the abstract singleton
}
