<?php

class Zigra_Route_Collection
{
    public $routes;

    public function __construct()
    {
        $this->routes = array();
    }

    /**
     * @param string $name
     */
    public function Add($name, $route = null)
    {
        // return if we have an empty input
        if (empty($name)) {
            throw new InvalidArgumentException('Zigra_Route_Collection->Add: $name was empty!');
        }

        if (is_null($route)) {
            throw new InvalidArgumentException('Zigra_Route_Collection->Add: $route was null!');
        }

        $this->routes[$name] = $route;
    }
}
