<?php

class Zigra_Request
{
    private const VALID_REQUEST_METHODS = ['GET', 'POST', 'PUT', 'HEAD'];

    protected string $_controller;
    protected string $_action;
    protected array $_args = [];
    protected string $_request;
    protected string $_method = 'GET';

    public function __construct($urlPath = null)
    {
        $this->_request = $urlPath ?? ($_SERVER['REQUEST_URI'] ?? '/');

        $parts = explode('/', (string)$this->_request);
        $parts = array_filter($parts);

        $this->_controller = (($c = array_shift($parts)) ? $c : 'index') . 'Controller';
        $this->_action = (($c = array_shift($parts)) ? $c : 'index');
        $this->_args = $parts ?: [];

        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        if (\in_array(strtoupper($method), self::VALID_REQUEST_METHODS, true)) {
            $this->_method = strtoupper($method);
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

    public function getAction(): string
    {
        return $this->_action;
    }

    public function getArgs(): array
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
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && 'XMLHTTPREQUEST' === strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']);
    }
}
