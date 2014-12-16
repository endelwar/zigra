<?php

class Zigra_Request
{
    protected $_controller;
    protected $_action;
    protected $_args;
    protected $_request;
    protected $_method;

    public $valid_request_methods = array('GET', 'POST', 'PUT', 'HEAD');

    public function __construct($urlPath = null)
    {
        $this->_request = $urlPath !== null ? $urlPath : $_SERVER['REQUEST_URI'];

        $parts = explode('/', $urlPath !== null ? $urlPath : $_SERVER['REQUEST_URI']);
        $parts = array_filter($parts);

        $this->_controller = (($c = array_shift($parts)) ? $c : 'index') . 'Controller';
        $this->_action = (($c = array_shift($parts)) ? $c : 'index');
        $this->_args = (isset($parts[0])) ? $parts : array();

        if (in_array(strtoupper($_SERVER['REQUEST_METHOD']), $this->valid_request_methods)) {
            $this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
        } else {
            $this->_method = null;
        }
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->_request;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getArgs()
    {
        return $this->_args;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function isPost()
    {
        if ($this->_method == 'POST') {
            return true;
        }
        return false;
    }

    public function isGet()
    {
        if ($this->_method == 'GET') {
            return true;
        }
        return false;
    }
}
