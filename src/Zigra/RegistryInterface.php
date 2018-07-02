<?php

interface Zigra_RegistryInterface
{
    /**
     * * Returns the variable "key" or "default" if not set
     *
     * @param string $key the variable's name
     * @param string|null $default optional default value if key not set
     *
     * @return mixed the variable's value or default
     */
    public static function get($key, $default = null);

    /**
     * Set a variable in the pool
     *
     * @param string $key the variable's name
     * @param mixed $value the variable's value
     * @return mixed
     */
    public static function set($key, $value);

    /**
     * Add a variable in the $key array
     *
     * @param string $key the variable's name
     * @param string $value the variable's value
     * @return mixed
     */
    public static function add($key, $value);

    /**
     * Check if variable exists
     *
     * @param string $key the variable's name
     *
     * @return bool
     */
    public static function has($key);

    /**
     * Retrieves an array of parameters.
     *
     * @return array An associative array of parameters
     */
    public static function getAll();

    /**
     * Retrieves an array of parameter names.
     *
     * @return array An indexed array of parameter names
     */
    public static function getKeys();

    /**
     * Clear all saved values in registry
     */
    public static function clear();
}
