<?php

class Zigra_Request
{
    protected string $_controller;
    protected $_action;
    protected $_args;
    protected $_request;
    protected string $_method;

    public array $valid_request_methods = ['GET', 'POST', 'PUT', 'HEAD'];

    public function __construct($urlPath = null)
    {
        $this->_request = $urlPath ?? $_SERVER['REQUEST_URI'];

        $parts = explode('/', (string) $this->_request);
        $parts = array_filter($parts);

        $this->_controller = (($c = array_shift($parts)) ? $c : 'index') . 'Controller';
        $this->_action = (($c = array_shift($parts)) ? $c : 'index');
        $this->_args = isset($parts[0]) ? $parts : [];

        if (in_array(strtoupper((string) $_SERVER['REQUEST_METHOD']), $this->valid_request_methods, true)) {
            $this->_method = strtoupper((string) $_SERVER['REQUEST_METHOD']);
        }
    }

    public function getRequest(): string
    {
        return $this->_request;
    }

    public function getController(): string
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

    public function getMethod(): string
    {
        return $this->_method;
    }

    public function isPost(): bool
    {
        return 'POST' === $this->_method;
    }

    public function isGet(): bool
    {
        return 'GET' === $this->_method;
    }

    public function isAjax(): bool
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return false;
        }
        if ('XMLHTTPREQUEST' === strtoupper((string) $_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return true;
        }

        return false;
    }
}
