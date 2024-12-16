<?php

interface Zigra_RegistryInterface
{
    /**
     * Returns the variable "key" or "default" if not set.
     *
     * @param string      $key     the variable's name
     * @param string|null $default optional default value if key not set
     *
     * @return mixed the variable's value or default
     */
    public static function get(string $key, ?string $default = null);

    /**
     * Set a variable in the pool.
     *
     * @param string $key   the variable's name
     * @param mixed  $value the variable's value
     */
    public static function set(string $key, $value);

    /**
     * Add a variable in the $key array.
     *
     * @param string $key   the variable's name
     * @param string $value the variable's value
     */
    public static function add(string $key, string $value);

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
