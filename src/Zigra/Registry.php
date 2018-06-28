<?php

/**
 * Class Zigra_Registry
 *
 * This was born as a simple Registry object, from Zigra version 0.9 Registry methods are both static and non static.
 * Why? Backward compatibility WTF! Beware lot of (fully tested) 💩💩💩💩 code below 💩💩💩💩
 *
 * @method get($key)
 * @method set($key, $value)
 * @method add($key, $value)
 * @method has($key)
 * @method getAll()
 * @method getKeys()
 */
class Zigra_Registry
{
    protected static $instance;

    /**
     * @var array
     */
    protected $pool = [];

    /**
     * @return Zigra_Registry
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // Note: value of $name is case sensitive.
        //echo "Calling object method '$name' " . implode(', ', $arguments) . "\n";
        switch ($name) {
            case 'get':
            case 'set':
            case 'add':
            case 'has':
            case 'getAll':
            case 'getKeys':
            case 'clear':
                $privateMethod = 'private' . ucfirst($name);
                return call_user_func_array([static::class, $privateMethod], $arguments);
                break;
            default:
                throw new \RuntimeException('Method not implemented');
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        // Note: value of $name is case sensitive.
        //echo "Calling static method '$name' " . implode(', ', $arguments) . "\n";
        switch ($name) {
            case 'get':
            case 'set':
            case 'add':
            case 'has':
            case 'getAll':
            case 'getKeys':
            case 'clear':
                $instance = static::getInstance();
                $privateMethod = 'private' . ucfirst($name);
                return call_user_func_array([$instance, $privateMethod], $arguments);
                break;
            default:
                throw new \RuntimeException('Method not implemented');
        }
    }

    /**
     * Get key's value as if key is an object property
     * @param string $key the variable's name
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->privateGet($key);
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
        $this->privateSet($key, $value);
    }

    /**
     * get
     *
     * Returns the variable "key" or "default" if not set
     *
     * @param string $key the variable's name
     * @param string $default optional default value if key not set
     *
     * @return mixed the variable's value or default
     */
    private function privateGet($key, $default = null)
    {
        return $this->privateHas($key) ? $this->pool[$key] : $default;
    }

    /**
     * Set a variable in the pool
     *
     * @param string $key the variable's name
     * @param string $value the variable's value
     *
     * @throws \InvalidArgumentException when $value is not defined
     */
    private function privateSet($key, $value = null)
    {
        if (func_num_args() >= 2) {
            $this->pool[$key] = $value;
        } else {
            throw new \InvalidArgumentException('Missing required value for key');
        }
    }

    /**
     * Add a variable in the $key array
     *
     * @param string $key the variable's name
     * @param string $value the variable's value
     *
     * @throws \InvalidArgumentException when $value is not defined
     */
    private function privateAdd($key, $value = null)
    {
        if (func_num_args() >= 2) {
            if ($this->privateHas($key) && is_array($this->pool[$key])) {
                $this->pool[$key][] = $value;
            }
        } else {
            throw new \InvalidArgumentException('Missing required value for key');
        }
    }

    /**
     * Check if variable exists
     *
     * @param string $key the variable's name
     *
     * @return bool
     */
    private function privateHas($key)
    {
        return array_key_exists($key, $this->pool);
    }

    /**
     * Retrieves an array of parameter names.
     *
     * @return array An indexed array of parameter names
     */
    private function privateGetKeys()
    {
        return array_keys($this->pool);
    }

    /**
     * Retrieves an array of parameters.
     *
     * @return array An associative array of parameters
     */
    private function privateGetAll()
    {
        return $this->pool;
    }

    public function privateClear()
    {
        $this->pool = [];
    }
}
