<?php

declare(strict_types=1);

namespace ZigraTest;

use PHPUnit\Framework\TestCase;
use Zigra\Exception;
use Zigra\Route;
use Zigra\Route\Compiler;

class RouteTest extends TestCase
{
    /**
     * @covers \Zigra\Route::setDefaults()
     * @covers \Zigra\Route::getDefaults()
     */
    public function testGetSetDefaults(): void
    {
        $route = new Route('/testing');
        $route->setDefaults(['controller' => 'test', 'action' => 'show', 'otherdefault' => true]);
        self::assertEquals(['controller' => 'test', 'action' => 'show', 'otherdefault' => true], $route->getDefaults());
    }

    /**
     * @covers \Zigra\Route::setCompilerClass()
     * @covers \Zigra\Route::getCompilerClass()
     */
    public function testGetSetCompilerClass(): void
    {
        $route = new Route('/testing', [], [], \Zigra\Route\Compiler::class);
        self::assertEquals(Compiler::class, $route->getCompilerClass());

        $route2 = new Route('/testing');
        self::assertEquals(Compiler::class, $route2->getCompilerClass());

        $mockCompiler = new class() implements Route\CompilerInterface {
            public function compile(Route $route)
            {
                return [];
            }
        };

        $route2->setCompilerClass(get_class($mockCompiler));
        self::assertEquals(get_class($mockCompiler), $route2->getCompilerClass());
    }

    public function testNotExistsCompilerClass(): void
    {
        $route = new Route('/bad-compiler');

        $this->expectException(Exception::class);
        $route->setCompilerClass('iDoNotExistsAsAClass');
    }

    public function testNotInstanceOfCompilerInterface(): void
    {
        $route = new Route('/bad-compiler2');

        $this->expectException(Exception::class);
        $route->setCompilerClass(Route::class);
    }

    /**
     * @dataProvider generateProvider
     */
    public function testGenerate($pattern, $parameter, $expected): void
    {
        $route = new Route($pattern);
        self::assertEquals($expected, $route->generate($parameter));
    }

    public function generateProvider(): array
    {
        return [
            'simple route' => ['/generate', [], '/generate'],
            'simple route without leading slash' => ['generate', [], '/generate'],
            'route with parameters' => ['/generate/{first}-{second}', ['first' => 'a', 'second' => 'b'], '/generate/a-b'],
            'route with parameters slash separated' => ['/generate/{first}/{second}', ['first' => 'a', 'second' => 'b'], '/generate/a/b'],
        ];
    }

    public function testGenerateWithUnrequiredParameters(): void
    {
        $route = new Route('/no-parameter-at-all');
        $this->expectException(\InvalidArgumentException::class);
        $route->generate(['first' => 'a', 'second' => 'b']);
    }

    public function testGenerateMissingAllParameters(): void
    {
        $route = new Route('/missing-all/{first}/{second}');
        $this->expectException(\InvalidArgumentException::class);
        $route->generate([]);
    }

    public function testGenerateWithWrongParameterNames(): void
    {
        $route = new Route('/wrong-parameter-names/{first}/{second}');
        $this->expectException(\InvalidArgumentException::class);
        $route->generate(['first' => 'a', 'last' => 'b']);
    }

    /**
     * @dataProvider generateWithRequirementsProvider
     */
    public function testGenerateWithRequirements($pattern, $parameter, $requirements, $expected): void
    {
        $route = new Route($pattern, [], $requirements);
        self::assertEquals($expected, $route->generate($parameter));
        foreach ($requirements as $key => $value) {
            $compiledRoute = $route->compile();
            self::assertStringContainsString($value, $compiledRoute['regex']);
        }
    }

    public function generateWithRequirementsProvider(): array
    {
        return [
            'digit' => ['/path/{digit}', ['digit' => 123], ['digit' => '\d+'], '/path/123'],
            'string' => ['/path/{string}', ['string' => 'abc'], ['string' => '\w+'], '/path/abc'],
        ];
    }

    public function testDoubleCompileRoute()
    {
        $route = new Route('/here/{first}/{second}');
        $firstPass = $route->compile();
        $secondPass = $route->compile();
        self::assertEquals($firstPass, $secondPass);
    }
}
