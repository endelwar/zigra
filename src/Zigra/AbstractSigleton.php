<?php

class Zigra_AbstractSigleton
{
    private static array $instances = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    final public static function getInstance()
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }
}
