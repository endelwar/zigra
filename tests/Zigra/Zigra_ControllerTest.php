<?php

use PHPUnit\Framework\TestCase;

class Zigra_ControllerTest extends TestCase
{
    private $request;
    private $params;
    private $controller;
    private static array $headers = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Configura $_SERVER
        $_SERVER = [
            'HTTP_HOST' => 'localhost',
            'REQUEST_URI' => '/example/123',
            'REQUEST_METHOD' => 'GET',
            'HTTPS' => 'on',
        ];

        // Crea un'istanza di Zigra_Request
        $this->request = new Zigra_Request('/example/123');
        $this->params = ['id' => '123'];
        $this->controller = new Zigra_Controller($this->request, $this->params);

        // Configura il percorso dinamico per i controller
        Zigra_Router::setControllerPath(__DIR__ . '/../mock/controllers/');

        // Aggiunge la route di esempio al router
        Zigra_Router::map(
            'example_route',
            '/example/{id}',
            ['controller' => 'Example', 'action' => 'view'],
            ['id' => '\d+']
        );
    }

    public function testRouterIntegration()
    {
        // Esegui il routing
        Zigra_Router::route($this->request);

        // Verifica che l'URL generato sia corretto
        $url = Zigra_Router::generate('example_route', ['id' => 123]);
        $this->assertSame('/example/123', $url);
    }

    public function testGetRequest()
    {
        $this->assertSame($this->request, $this->controller->getRequest());
    }

    public function testGetParams()
    {
        $this->assertSame($this->params, $this->controller->getParams());
    }

    public function testGetParamExists()
    {
        $this->assertSame('123', $this->controller->getParam('id'));
    }

    public function testGetParamNotExists()
    {
        $this->assertNull($this->controller->getParam('nonexistent'));
    }

    public function testForward()
    {
        self::markTestIncomplete();
        try {
            $this->controller->forward('example_route', ['id' => 456], 302, 'section1');
        } catch (Exception $e) {
            // Ignoriamo l'exit
        }

        $this->assertContains('HTTP/1.1 302 Found', xdebug_get_headers());
        $this->assertContains('Location: /example/456#section1', xdebug_get_headers());
    }

    public function testForward404()
    {
        self::markTestIncomplete();
        try {
            $this->controller->forward404('Page not found');
        } catch (Exception $e) {
            // Ignoriamo l'exit
        }

        $this->assertContains('HTTP/1.1 404 Not Found', xdebug_get_headers());
    }

    public function testRedirect()
    {
        self::markTestIncomplete();
        try {
            $this->controller->redirect('/new-location', 301);
        } catch (Exception $e) {
            // Ignoriamo l'exit
        }

        $this->assertContains('Location: /new-location', xdebug_get_headers());
        $this->assertContains('HTTP/1.1 301 Moved Permanently', xdebug_get_headers());
    }
}
