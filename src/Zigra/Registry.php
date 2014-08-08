<?php

class Zigra_RegistryException extends Exception
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}

class Zigra_Registry implements IteratorAggregate
{

    protected static $instance = null;

    /**
     * Array che conterrà le coppie chiave => valore salvate
     * nel registro.
     */
    protected $pool;

    public function __construct()
    {
        $this->pool = array();
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }


    /**
     * Restituisce l’iteratore che per comodità è uno di quelli
     * implementati nella SPL.
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
     * */

    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->pool[$key] : $default;
    }

    /**
     * set
     *
     * Set a variable in the pool
     *
     * @param string $key the variable's name
     * @param string $value the variable's value
     *
     * @return void
     * */
    public function set($key, $value)
    {
        $this->pool[$key] = $value;
    }

    /**
     * add
     *
     * Add a variable in the $key array
     *
     * @param string $key the variable's name
     * @param string $value the variable's value
     *
     * @return void
     * */
    public function add($key, $value)
    {
        if ($this->has($key) && is_array($this->pool[$key])) {
            $this->pool[$key][] = $value;
        }
    }

    /**
     * has
     *
     * Check if variable exists
     *
     * @param string $key the variable's name
     *
     * @return boolean
     * */
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
     * Funzioni di utilità per accedere alle chiavi come se fossero
     * proprietà del registro.
     *
     * @param string $key the variable's name
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Funzioni di utilità per accedere alle chiavi come se fossero
     * proprietà del registro.
     *
     * @param string $key the variable's name
     * @param mixed $value the variable's value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
}
