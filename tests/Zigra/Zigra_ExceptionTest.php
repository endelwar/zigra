<?php

use PHPUnit\Framework\TestCase;

class Zigra_ExceptionTest extends TestCase
{
    public function testRenderErrorInCli(): void
    {
        // PHPUnit gira in CLI, quindi `isCommandLineInterface` sarà sempre true.
        $this->expectOutputString("404: Test error message");
        Zigra_Exception::renderError("404", "Test error message");
    }

    public function testDisplayException(): void
    {
        $exception = new Exception("Test Exception", 500);
        $expectedOutput = "500<hr>Test Exception<hr>" . $exception->getLine() . "<hr><pre>" . $exception->getTraceAsString() . "</pre>";

        $this->expectOutputString($expectedOutput);
        Zigra_Exception::displayException($exception);
    }
}
