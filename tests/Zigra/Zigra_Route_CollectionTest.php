<?php

use PHPUnit\Framework\TestCase;

class Zigra_Route_CollectionTest extends TestCase
{
    public function testConstructorInitializesEmptyRoutes(): void
    {
        $collection = new Zigra_Route_Collection();

        $this->assertIsArray($collection->routes);
        $this->assertEmpty($collection->routes);
    }

    public function testAddRoute(): void
    {
        $collection = new Zigra_Route_Collection();

        $route = new Zigra_Route('/home', ['controller' => 'HomeController', 'action' => 'index']);
        $collection->add('home', $route);

        $this->assertArrayHasKey('home', $collection->routes);
        $this->assertSame($route, $collection->routes['home']);
    }

    public function testAddThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Zigra_Route_Collection->Add: $name was empty!');

        $collection = new Zigra_Route_Collection();
        $route = new Zigra_Route('/home', ['controller' => 'HomeController', 'action' => 'index']);
        $collection->add('', $route);
    }

    public function testAddThrowsExceptionForNullRoute(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Zigra_Route_Collection->Add: $route was null!');

        $collection = new Zigra_Route_Collection();
        $collection->add('home', null);
    }

    public function testOverwriteExistingRoute(): void
    {
        $collection = new Zigra_Route_Collection();

        $firstRoute = new Zigra_Route('/home', ['controller' => 'HomeController', 'action' => 'index']);
        $secondRoute = new Zigra_Route('/dashboard', ['controller' => 'DashboardController', 'action' => 'index']);

        $collection->add('home', $firstRoute);
        $collection->add('home', $secondRoute);

        $this->assertCount(1, $collection->routes);
        $this->assertSame($secondRoute, $collection->routes['home']);
    }
}
