<?php

class Zigra_Registry_Tplvar extends Zigra_Registry
{
    protected static $instance;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->pool = array();
    }
}
