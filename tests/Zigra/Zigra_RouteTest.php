<?php

use PHPUnit\Framework\TestCase;

class Zigra_RouteTest extends TestCase
{
    public function testSetAndGetPattern(): void
    {
        $route = new Zigra_Route('/test');
        $this->assertSame('/test', $route->getPattern());

        $route->setPattern('example');
        $this->assertSame('/example', $route->getPattern());
    }

    public function testSetAndGetDefaults(): void
    {
        $defaults = ['controller' => 'TestController', 'action' => 'index'];
        $route = new Zigra_Route('/test', $defaults);

        $this->assertSame($defaults, $route->getDefaults());
    }

    public function testSetAndGetRequirements(): void
    {
        $requirements = ['id' => '^\d+$', 'slug' => '^[a-z-]+$'];
        $route = new Zigra_Route('/test', [], $requirements);

        $expectedRequirements = ['id' => '\d+', 'slug' => '[a-z-]+'];
        $this->assertSame($expectedRequirements, $route->getRequirements());
    }

    public function testSetAndGetOptions(): void
    {
        $options = ['compiler_class' => 'Custom_Compiler'];
        $route = new Zigra_Route('/test', [], [], $options);

        $this->assertSame($options, $route->getOptions());
        $this->assertSame('Custom_Compiler', $route->getOption('compiler_class'));
    }

    public function testSanitizeRequirement(): void
    {
        $route = new Zigra_Route('/test');
        $reflection = new ReflectionClass($route);
        $method = $reflection->getMethod('sanitizeRequirement');
        $method->setAccessible(true);

        $this->assertSame('\d+', $method->invoke($route, '^\d+$'));
        $this->assertSame('[a-z-]+', $method->invoke($route, '^[a-z-]+$'));
    }

    public function testCompile(): void
    {
        // Crea un'istanza della classe Zigra_Route
        $route = new Zigra_Route(
            '/user/{id}',
            ['controller' => 'UserController', 'action' => 'show'],
            ['id' => '\d+']
        );

        // Configura il compilatore reale
        $route->setOptions(['compiler_class' => Zigra_Route_Compiler::class]);

        // Esegui il metodo compile
        $compiled = $route->compile();

        // Array atteso
        $expected = [
            [
                'pattern' => '/user/{id}',
                'regex' => '@^\/user\/(?P<id>\d+)$@',
                'variables' => ['id'],
                'defaults' => ['controller' => 'UserController', 'action' => 'show'],
            ],
        ];

        // Verifica il risultato
        $this->assertSame($expected, $compiled);
    }

    public function testGenerateSuccess(): void
    {
        $compiledRoute = [
            ['pattern' => '/user/{id}', 'variables' => ['id']],
        ];
        $route = $this->getMockBuilder(Zigra_Route::class)
            ->setConstructorArgs(['/test'])
            ->onlyMethods(['compile'])
            ->getMock();

        $route->expects($this->once())
            ->method('compile')
            ->willReturn($compiledRoute);

        $this->assertSame('/user/123', $route->generate(['id' => 123]));
    }

    public function testGenerateThrowsExceptionForNoParameters(): void
    {
        $compiledRoute = [
            ['pattern' => '/user/{id}', 'variables' => ['id']],
        ];
        $route = $this->getMockBuilder(Zigra_Route::class)
            ->setConstructorArgs(['/test'])
            ->onlyMethods(['compile'])
            ->getMock();

        $route->expects($this->once())
            ->method('compile')
            ->willReturn($compiledRoute);

        $this->expectException(InvalidArgumentException::class);
        $route->generate([]);
    }

    public function testGenerateThrowsExceptionForMissingParameters(): void
    {
        $compiledRoute = [
            ['pattern' => '/user/{id}', 'variables' => ['id', 'slug']],
        ];
        $route = $this->getMockBuilder(Zigra_Route::class)
            ->setConstructorArgs(['/test'])
            ->onlyMethods(['compile'])
            ->getMock();

        $route->expects($this->once())
            ->method('compile')
            ->willReturn($compiledRoute);

        $this->expectException(InvalidArgumentException::class);
        $route->generate(['id' => 123]);
    }
}
