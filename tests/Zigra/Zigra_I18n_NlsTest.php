<?php

use PHPUnit\Framework\TestCase;

class Zigra_I18n_NlsTest extends TestCase
{
    protected $nlsData;

    protected function setUp(): void
    {
        parent::setUp();

        // Carica un file di lingua reale
        $this->nlsData = include __DIR__ . '/../../src/Zigra/I18n/nls/it_IT.nls.php';
    }

    public function testInitWithValidLanguage()
    {
        $nls = Zigra_I18n_Nls::init('it_IT');

        $this->assertInstanceOf(Zigra_I18n_Nls::class, $nls);
        $this->assertSame('it_IT', $nls->key());
        $this->assertSame('Italian', $nls->fullname());
    }

    public function testInitWithInvalidLanguage()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot load language file');

        Zigra_I18n_Nls::init('non_existent');
    }

    public function testFromArray()
    {
        $nls = Zigra_I18n_Nls::fromArray($this->nlsData);

        $this->assertSame('it_IT', $nls->key());
        $this->assertSame('Italian', $nls->fullname());
        $this->assertSame('UTF-8', $nls->encoding());
        $this->assertSame('ltr', $nls->direction());
        $this->assertContains('italiano', $nls->aliases());
    }

    public function testMatchesWithExactMatch()
    {
        $nls = Zigra_I18n_Nls::fromArray($this->nlsData);

        $this->assertTrue($nls->matches('it'));
        $this->assertTrue($nls->matches('italiano'));
        $this->assertTrue($nls->matches('it_IT'));
    }

    public function testMatchesWithNoMatch()
    {
        $nls = Zigra_I18n_Nls::fromArray($this->nlsData);

        $this->assertFalse($nls->matches('es_ES'));
        $this->assertFalse($nls->matches('nonexistent'));
    }

    public function testIsocode()
    {
        $nls = Zigra_I18n_Nls::fromArray($this->nlsData);

        $this->assertSame('it', $nls->isocode());
    }

    public function testLocale()
    {
        $nls = Zigra_I18n_Nls::fromArray($this->nlsData);

        $this->assertSame('it,it_IT,it_IT.utf8,it_IT.utf-8,it_IT.UTF-8,it_IT@euro,italian,Italian_Italy.1252', $nls->locale());
    }

    public function testEncoding()
    {
        $nls = Zigra_I18n_Nls::fromArray($this->nlsData);

        $this->assertSame('UTF-8', $nls->encoding());
    }

    public function testDirection()
    {
        $nls = Zigra_I18n_Nls::fromArray($this->nlsData);

        $this->assertSame('ltr', $nls->direction());
    }

    public function testDisplay()
    {
        $data = [
            'englishlang' => ['it_IT' => 'Italian'],
            'language' => ['it_IT' => 'Italiano'],
            'locale' => ['it_IT' => 'it_IT.UTF-8'],
            'encoding' => ['it_IT' => 'UTF-8'],
            'direction' => ['it_IT' => 'ltr'],
            'regionsubtag' => 'it_IT',
        ];

        $nls = Zigra_I18n_Nls::fromArray($data);
        $this->assertNull($nls->display(), 'Display should initially be null.');

        // Forza un valore di display
        $reflection = new ReflectionClass($nls);
        $property = $reflection->getProperty('display');
        $property->setAccessible(true);
        $property->setValue($nls, 'Custom Display');

        $this->assertSame('Custom Display', $nls->display(), 'Display should return the forced value.');
    }

    public function testRegionSubtag()
    {
        $data = [
            'englishlang' => ['ja_JP' => 'Japanese'],
            'language' => ['ja_JP' => '日本語'],
            'locale' => ['ja_JP' => 'ja_JP.UTF-8'],
            'encoding' => ['ja_JP' => 'UTF-8'],
            'direction' => ['ja_JP' => 'ltr'],
            'regionsubtag' => 'ja_JP',
        ];

        $nls = Zigra_I18n_Nls::fromArray($data);
        $this->assertSame('ja_JP', $nls->regionsubtag(), 'Region subtag should match the expected value.');
    }
}
