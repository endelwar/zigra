<?php

declare(strict_types=1);

namespace ZigraTest\Route;

use PHPUnit\Framework\TestCase;
use Zigra\Exception;
use Zigra\Route;
use Zigra\Route\Collection;

class CollectionTest extends TestCase
{
    private $collection;

    public function setUp(): void
    {
        $this->collection = new Collection();
    }

    public function tearDown(): void
    {
        $this->collection = null;
    }

    public function testAdd(): void
    {
        $this->collection->add('test', new Route('/test'));
        self::assertTrue($this->collection->has('test'));
        self::assertFalse($this->collection->has('not-here'));
        self::assertNotEmpty($this->collection->getAll());
        self::assertCount(1, $this->collection->getAll());
    }

    public function testExceptionAdd(): void
    {
        $this->expectException(Exception::class);
        $this->collection->add('', new Route(''));
    }
}
