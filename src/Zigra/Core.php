<?php

class Zigra_Core
{
    /**
     * Path to Zigra root.
     *
     * @var string|null Zigra root directory
     */
    private static ?string $path = null;

    /**
     * Set the path to your core Zigra libraries.
     *
     * @param string|null $path The path to your Zigra libraries
     */
    public static function setPath(?string $path): void
    {
        self::$path = $path ? rtrim($path, \DIRECTORY_SEPARATOR) : \dirname(__DIR__);
    }

    /**
     * Get the root path to Zigra.
     */
    public static function getPath(): string
    {
        if (!self::$path) {
            self::$path = \dirname(__DIR__);
        }

        return self::$path;
    }

    /**
     * Registers Zigra as an SPL autoloader.
     */
    public static function register(): void
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register([new self(), 'autoload']);
    }

    /**
     * simple autoload function
     * returns true if the class was loaded, otherwise false.
     *
     * @param string $className name of the class to load
     */
    public static function autoload(string $className): bool
    {
        if (class_exists($className, false)
            || interface_exists($className, false)
            || 0 !== stripos($className, 'Zigra')
        ) {
            return false;
        }

        $class = self::getPath() . \DIRECTORY_SEPARATOR . str_replace('_', \DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists($class)) {
            include $class;

            return true;
        }

        return false;
    }
}
