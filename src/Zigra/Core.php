<?php

class Zigra_Core
{
    /**
     * Path to Zigra root
     *
     * @var string $path Zigra root directory
     */
    private static $path;

    /**
     * Set the path to your core Zigra libraries
     *
     * @param string $path The path to your Zigra libraries
     *
     * @return void
     */
    public static function setPath($path)
    {
        self::$path = $path;
    }

    /**
     * Get the root path to Zigra
     *
     * @return string
     */
    public static function getPath()
    {
        if (!self::$path) {
            self::$path = realpath(__DIR__ . '/..');
        }

        return self::$path;
    }

    /**
     * Registers Zigra as an SPL autoloader.
     *
     * @return void
     */
    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self(), 'autoload'));
    }

    /**
     * simple autoload function
     * returns true if the class was loaded, otherwise false
     *
     * @param string $className name of the class to load
     *
     * @return bool
     */
    public static function autoload($className)
    {
        if (0 !== stripos($className, 'Zigra')
            || class_exists($className, false)
            || interface_exists($className, false)
        ) {
            return false;
        }

        $class = self::getPath() . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists($class)) {
            include $class;

            return true;
        }

        return false;
    }
}
