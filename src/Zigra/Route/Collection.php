<?php

class Zigra_Route_Collection
{
    public array $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function add(string $name, $route = null): void
    {
        // return if we have an empty input
        if (empty($name)) {
            throw new InvalidArgumentException('Zigra_Route_Collection->Add: $name was empty!');
        }

        if (null === $route) {
            throw new InvalidArgumentException('Zigra_Route_Collection->Add: $route was null!');
        }

        $this->routes[$name] = $route;
    }
}
