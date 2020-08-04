<?php

declare(strict_types=1);

namespace Zigra;

interface RegistryInterface
{
    /**
     * * Returns the variable "key" or "default" if not set.
     *
     * @param string     $key     the variable's name
     * @param mixed|null $default optional default value if key not set
     *
     * @return mixed the variable's value or default
     */
    public static function get(string $key, $default = null);

    /**
     * Set a variable in the pool.
     *
     * @param string $key   the variable's name
     * @param mixed  $value the variable's value
     */
    public static function set(string $key, $value): void;

    /**
     * Add a variable in the $key array.
     *
     * @param string $key   the variable's name
     * @param mixed  $value the variable's value
     */
    public static function add(string $key, $value): void;

    /**
     * Check if variable exists.
     *
     * @param string $key the variable's name
     */
    public static function has(string $key): bool;

    /**
     * Retrieves an array of parameters.
     *
     * @return array An associative array of parameters
     */
    public static function getAll(): array;

    /**
     * Retrieves an array of parameter names.
     *
     * @return array An indexed array of parameter names
     */
    public static function getKeys(): array;

    /**
     * Clear all saved values in registry.
     */
    public static function clear();
}
