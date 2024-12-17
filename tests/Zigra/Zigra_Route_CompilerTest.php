<?php

use PHPUnit\Framework\TestCase;

class Zigra_Route_CompilerTest extends TestCase
{
    public function testCompileWithSingleVariable(): void
    {
        $route = new Zigra_Route(
            '/user/{id}',
            ['controller' => 'UserController', 'action' => 'show'],
            ['id' => '\d+']
        );

        $compiler = new Zigra_Route_Compiler();
        $compiled = $compiler->compile($route);

        $expected = [
            [
                'pattern' => '/user/{id}',
                'regex' => '@^\/user\/(?P<id>\d+)$@',
                'variables' => ['id'],
                'defaults' => ['controller' => 'UserController', 'action' => 'show'],
            ],
        ];

        $this->assertSame($expected, $compiled);
    }

    public function testCompileWithMultipleVariables(): void
    {
        $route = new Zigra_Route(
            '/user/{id}/post/{slug}',
            ['controller' => 'PostController', 'action' => 'view'],
            ['id' => '\d+', 'slug' => '[a-z-]+']
        );

        $compiler = new Zigra_Route_Compiler();
        $compiled = $compiler->compile($route);

        $expected = [
            [
                'pattern' => '/user/{id}/post/{slug}',
                'regex' => '@^\/user\/(?P<id>\d+)\/post\/(?P<slug>[a-z-]+)$@',
                'variables' => ['id', 'slug'],
                'defaults' => ['controller' => 'PostController', 'action' => 'view'],
            ],
        ];

        $this->assertSame($expected, $compiled);
    }

    public function testCompileWithoutVariables(): void
    {
        $route = new Zigra_Route(
            '/about',
            ['controller' => 'PageController', 'action' => 'about']
        );

        $compiler = new Zigra_Route_Compiler();
        $compiled = $compiler->compile($route);

        $expected = [
            [
                'pattern' => '/about',
                'regex' => '@^\/about$@',
                'variables' => [],
                'defaults' => ['controller' => 'PageController', 'action' => 'about'],
            ],
        ];

        $this->assertSame($expected, $compiled);
    }

    public function testCompileWithDefaultRequirements(): void
    {
        $route = new Zigra_Route(
            '/user/{id}',
            ['controller' => 'UserController', 'action' => 'profile']
        );

        $compiler = new Zigra_Route_Compiler();
        $compiled = $compiler->compile($route);

        $expected = [
            [
                'pattern' => '/user/{id}',
                'regex' => '@^\/user\/(?P<id>[^\/]+)$@',
                'variables' => ['id'],
                'defaults' => ['controller' => 'UserController', 'action' => 'profile'],
            ],
        ];

        $this->assertSame($expected, $compiled);
    }

    public function testCompileWithEmptyPattern(): void
    {
        $route = new Zigra_Route(
            '',
            ['controller' => 'DefaultController', 'action' => 'index']
        );

        $compiler = new Zigra_Route_Compiler();
        $compiled = $compiler->compile($route);

        $expected = [
            [
                'pattern' => '/',
                'regex' => '@^\/$@',
                'variables' => [],
                'defaults' => ['controller' => 'DefaultController', 'action' => 'index'],
            ],
        ];

        $this->assertSame($expected, $compiled);
    }
}
