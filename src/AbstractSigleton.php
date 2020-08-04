<?php

declare(strict_types=1);

namespace Zigra;

class AbstractSigleton
{
    private static $instances = [];

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
