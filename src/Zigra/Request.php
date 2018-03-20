<?php

class Zigra_Request
{
    protected $_controller;
    protected $_action;
    protected $_args;
    protected $_request;
    protected $_method;

    public $valid_request_methods = ['GET', 'POST', 'PUT', 'HEAD'];

    public function __construct($urlPath = null)
    {
        $this->_request = $urlPath !== null ? $urlPath : $_SERVER['REQUEST_URI'];

        $parts = explode('/', $this->_request);
        $parts = array_filter($parts);

        $this->_controller = (($c = array_shift($parts)) ? $c : 'index') . 'Controller';
        $this->_action = (($c = array_shift($parts)) ? $c : 'index');
        $this->_args = isset($parts[0]) ? $parts : [];

        if (in_array(strtoupper($_SERVER['REQUEST_METHOD']), $this->valid_request_methods, true)) {
            $this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
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
        return $this->_method === 'POST';
    }

    public function isGet()
    {
        return $this->_method === 'GET';
    }

    public function isAjax()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return false;
        }
        if (strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) === 'XMLHTTPREQUEST') {
            return true;
        }

        return false;
    }
}
