<?php

declare(strict_types=1);

namespace Zigra\Route;

use Zigra\Exception;
use Zigra\Route;

class Collection
{
    private $routes = [];

    public function add(string $name, Route $route): void
    {
        if ('' === $name) {
            throw new Exception('Empty route name');
        }
        $this->routes[$name] = $route;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->routes);
    }

    public function getAll(): array
    {
        return $this->routes;
    }
}
