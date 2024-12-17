<?php

use PHPUnit\Framework\TestCase;

class Zigra_CoreTest extends TestCase
{
    private string $originalPath;

    protected function setUp(): void
    {
        // Salva il path originale per ripristinarlo dopo il test
        $this->originalPath = Zigra_Core::getPath();
    }

    protected function tearDown(): void
    {
        // Ripristina il path originale dopo ogni test
        Zigra_Core::setPath($this->originalPath);
    }

    public function testSetAndGetPath(): void
    {
        $customPath = '/custom/path/to/zigra';
        Zigra_Core::setPath($customPath);

        $this->assertSame($customPath, Zigra_Core::getPath());
    }

    public function testGetPathDefaultsToParentDirectory(): void
    {
        Zigra_Core::setPath(null); // Simula che non sia stato impostato nulla

        // Imposta l'aspettativa corretta basata sulla posizione della classe
        $expectedPath = \dirname(__DIR__, 2) . '/src';
        $this->assertSame($expectedPath, Zigra_Core::getPath());
    }

    public function testRegisterAutoloader(): void
    {
        // Registra l'autoloader
        Zigra_Core::register();

        // Verifica che l'autoloader sia stato registrato
        $autoloadFunctions = spl_autoload_functions();

        $this->assertNotEmpty($autoloadFunctions, 'No autoload functions registered');
        $this->assertTrue(
            \in_array([new Zigra_Core(), 'autoload'], $autoloadFunctions, true)
            || \in_array(['Zigra_Core', 'autoload'], $autoloadFunctions, true),
            'Zigra_Core::autoload was not registered as an autoloader'
        );
    }

    public function testAutoloadExistingClass(): void
    {
        // Simula un file PHP temporaneo per una classe Zigra
        $tempDir = sys_get_temp_dir();
        $className = 'Zigra_TestClass';
        $filePath = $tempDir . \DIRECTORY_SEPARATOR . 'Zigra' . \DIRECTORY_SEPARATOR . 'TestClass.php';

        // Crea la directory e il file di classe
        if (!is_dir(\dirname($filePath))) {
            mkdir(\dirname($filePath), 0777, true);
        }
        file_put_contents($filePath, '<?php class Zigra_TestClass {}');

        // Imposta il path a `sys_get_temp_dir`
        Zigra_Core::setPath($tempDir);

        // Verifica che la classe venga caricata
        $this->assertFalse(class_exists($className, false));
        $this->assertTrue(Zigra_Core::autoload($className));
        $this->assertTrue(class_exists($className, false));

        // Pulizia
        unlink($filePath);
        rmdir(\dirname($filePath));
    }

    public function testAutoloadNonExistingClass(): void
    {
        $className = 'Zigra_NonExistingClass';

        // Verifica che il metodo `autoload` restituisca false per una classe inesistente
        $this->assertFalse(Zigra_Core::autoload($className));
    }
}
