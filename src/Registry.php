<?php

declare(strict_types=1);

namespace Zigra;

/**
 * Class Registry.
 *
 * @method static Registry getInstance()
 */
class Registry extends AbstractSigleton implements RegistryInterface
{
    /**
     * @var array
     */
    protected static $pool = [];

    /**
     * Get key's value as if key is an object property.
     */
    public function __get(string $key)
    {
        return self::get($key);
    }

    /**
     * Set key's value as if key is an object property.
     */
    public function __set(string $key, $value)
    {
        self::set($key, $value);
    }

    public function __isset(string $key)
    {
        return self::has($key);
    }

    public static function set(string $key, $value): void
    {
        self::$pool[$key] = $value;
    }

    public static function add(string $key, $value): void
    {
        if (self::has($key) && is_array(self::$pool[$key])) {
            self::$pool[$key][] = $value;
        }
    }

    /**
     * @return mixed|null
     */
    public static function get(string $key, $default = null)
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
