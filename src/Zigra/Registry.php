<?php

class Zigra_Registry implements IteratorAggregate
{
    protected static $instance;

    /**
     * @var array $pool
     */
    protected $pool;

    public function __construct()
    {
        $this->pool = array();
    }

    /**
     * @return $this
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $c = __CLASS__;
            self::$instance = new $c();
        }

        return self::$instance;
    }

    /**
     * Return Iterator.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->pool);
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
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->pool[$key] : $default;
    }

    /**
     * Set a variable in the pool
     *
     * @param string $key the variable's name
     * @param string $value the variable's value
     *
     * @throws InvalidArgumentException when $value is not defined
     * @return void
     */
    public function set($key, $value = null)
    {
        if (func_num_args() >= 2) {
            $this->pool[$key] = $value;
        } else {
            throw new InvalidArgumentException('Missing required value for key');
        }
    }

    /**
     * Add a variable in the $key array
     *
     * @param string $key the variable's name
     * @param string $value the variable's value
     *
     * @throws InvalidArgumentException when $value is not defined
     * @return void
     */
    public function add($key, $value = null)
    {
        if (func_num_args() >= 2) {
            if ($this->has($key) && is_array($this->pool[$key])) {
                $this->pool[$key][] = $value;
            }
        } else {
            throw new InvalidArgumentException('Missing required value for key');
        }
    }

    /**
     * Check if variable exists
     *
     * @param string $key the variable's name
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->pool);
    }

    /**
     * Retrieves an array of parameter names.
     *
     * @access public
     * @return array An indexed array of parameter names
     */
    public function getNames()
    {
        return array_keys($this->pool);
    }

    /**
     * Retrieves an array of parameters.
     *
     * @access public
     * @return array An associative array of parameters
     */
    public function getAll()
    {
        return $this->pool;
    }

    /**
     * Get key's value as if key is an object property
     * @param string $key the variable's name
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set key's value as if key is an object property
     *
     * @param string $key the variable's name
     * @param mixed $value the variable's value
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
}
