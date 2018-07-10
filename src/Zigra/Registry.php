<?php

/**
 * Class Zigra_Registry
 *
 * @method static Zigra_Registry getInstance()
 */
class Zigra_Registry extends Zigra_AbstractSigleton implements Zigra_RegistryInterface
{
    /**
     * @var array
     */
    protected static $pool = [];

    /**
     * Get key's value as if key is an object property
     * @param string $key the variable's name
     *
     * @return mixed
     */
    public function __get($key)
    {
        return self::get($key);
    }

    /**
     * Set key's value as if key is an object property
     *
     * @param string $key the variable's name
     * @param mixed $value the variable's value
     *
     * @throws \InvalidArgumentException
     */
    public function __set($key, $value)
    {
        self::set($key, $value);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function set($key, $value)
    {
        self::$pool[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function add($key, $value)
    {
        if (self::has($key) && is_array(self::$pool[$key])) {
            self::$pool[$key][] = $value;
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public static function get($key, $default = null)
    {
        return self::has($key) ? self::$pool[$key] : $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return array_key_exists($key, self::$pool);
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        return self::$pool;
    }

    /**
     * @return array
     */
    public static function getKeys()
    {
        return array_keys(self::$pool);
    }

    public static function clear()
    {
        static::$pool = [];
    }
}
