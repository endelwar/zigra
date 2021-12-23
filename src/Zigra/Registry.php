<?php

/**
 * Class Zigra_Registry.
 *
 * @method static Zigra_Registry getInstance()
 */
class Zigra_Registry extends Zigra_AbstractSigleton implements Zigra_RegistryInterface
{
    protected static array $pool = [];

    /**
     * Get key's value as if key is an object property.
     *
     * @param string $key the variable's name
     */
    public function __get(string $key)
    {
        return self::get($key);
    }

    /**
     * Set key's value as if key is an object property.
     *
     * @param string $key   the variable's name
     * @param mixed  $value the variable's value
     *
     * @throws \InvalidArgumentException
     */
    public function __set(string $key, $value)
    {
        self::set($key, $value);
    }

    public static function set(string $key, $value)
    {
        self::$pool[$key] = $value;
    }

    public static function add(string $key, string $value)
    {
        if (is_array(self::$pool[$key]) && self::has($key)) {
            self::$pool[$key][] = $value;
        }
    }

    /**
     * @return mixed|null
     */
    public static function get(string $key, string $default = null)
    {
        return self::has($key) ? self::$pool[$key] : $default;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::$pool);
    }

    public static function getAll(): array
    {
        return self::$pool;
    }

    public static function getKeys(): array
    {
        return array_keys(self::$pool);
    }

    public static function clear()
    {
        static::$pool = [];
    }
}
