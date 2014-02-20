<?php
class Zigra_Request
{

    protected $_controller;
    protected $_action;
    protected $_args;
    protected $_request;
    protected $_method;

    public function __construct($urlPath = null)
    {
        $this->_request = $urlPath !== null ? $urlPath : $_SERVER['REQUEST_URI'];

        $parts = explode('/', $urlPath !== null ? $urlPath : $_SERVER['REQUEST_URI']);
        $parts = array_filter($parts);

        //var_dump('parts 1', $parts);

        $this->_controller = (($c = array_shift($parts)) ? $c : 'index') . 'Controller';
        //var_dump($this->_controller);
        $this->_action = (($c = array_shift($parts)) ? $c : 'index');
        //var_dump($this->_action);
        $this->_args = (isset($parts[0])) ? $parts : array();
        //var_dump($this->_args);

        switch (strtoupper($_SERVER['REQUEST_METHOD'])) {
            case 'GET':
                $this->_method = 'GET';
                break;
            case 'POST':
                $this->_method = 'POST';
                break;
            case 'PUT':
                $this->_method = 'PUT';
                break;
            case 'HEAD':
                $this->_method = 'HEAD';
                break;
            default:
                $this->_method = null;
        }


        //var_dump($this);
    }

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
