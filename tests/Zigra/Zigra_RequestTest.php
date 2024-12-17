<?php

use PHPUnit\Framework\TestCase;

class Zigra_RequestTest extends TestCase
{
    private array $serverBackup;

    protected function setUp(): void
    {
        parent::setUp();
        // Salva una copia dell'array $_SERVER
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        // Ripristina $_SERVER
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    public function testGetRequestWithDefaultUrl()
    {
        $_SERVER['REQUEST_URI'] = '/default/url';
        $request = new Zigra_Request();

        $this->assertSame('/default/url', $request->getRequest());
    }

    public function testGetRequestWithCustomUrl()
    {
        $request = new Zigra_Request('/custom/url');
        $this->assertSame('/custom/url', $request->getRequest());
    }

    public function testGetController()
    {
        $request = new Zigra_Request('/user/profile');
        $this->assertSame('userController', $request->getController());
    }

    public function testGetAction()
    {
        $request = new Zigra_Request('/user/profile');
        $this->assertSame('profile', $request->getAction());
    }

    public function testGetArgs()
    {
        $request = new Zigra_Request('/user/profile/123');
        $this->assertSame(['123'], $request->getArgs());
    }

    public function testGetArgsEmpty()
    {
        $request = new Zigra_Request('/user/profile');
        $this->assertSame([], $request->getArgs());
    }

    public function testGetMethodFromServer()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = new Zigra_Request();
        $this->assertSame('POST', $request->getMethod());
    }

    public function testGetMethodDefaultToGet()
    {
        unset($_SERVER['REQUEST_METHOD']);
        $request = new Zigra_Request();
        $this->assertSame('GET', $request->getMethod());
    }

    public function testIsPostTrue()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = new Zigra_Request();
        $this->assertTrue($request->isPost());
    }

    public function testIsPostFalse()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Zigra_Request();
        $this->assertFalse($request->isPost());
    }

    public function testIsGetTrue()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Zigra_Request();
        $this->assertTrue($request->isGet());
    }

    public function testIsGetFalse()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = new Zigra_Request();
        $this->assertFalse($request->isGet());
    }

    public function testIsAjaxTrue()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $request = new Zigra_Request();
        $this->assertTrue($request->isAjax());
    }

    public function testIsAjaxFalse()
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $request = new Zigra_Request();
        $this->assertFalse($request->isAjax());
    }
}
