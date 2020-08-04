<?php

declare(strict_types=1);

namespace Zigra;

class Core
{
    /**
     * Path to Zigra root.
     *
     * @var string Zigra root directory
     */
    private static $path;

    /**
     * Set the path to your core Zigra libraries.
     *
     * @param string $path The path to your Zigra libraries
     */
    public static function setPath($path): void
    {
        self::$path = $path;
    }

    /**
     * Get the root path to Zigra.
     */
    public static function getPath(): string
    {
        if (!self::$path) {
            self::$path = dirname(__DIR__) . '';
        }

        return self::$path;
    }
}
