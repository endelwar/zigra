<?php


class Zigra_Request
{

    protected $_controller;
    protected $_action;
    protected $_args;
    protected $_request;

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

        //var_dump($this);
    }

    public function GetRequest()
    {
        return $this->_request;
    }

    public function GetController()
    {
        return $this->_controller;
    }

    public function GetAction()
    {
        return $this->_action;
    }

    public function GetArgs()
    {
        return $this->_args;
    }


}

