<?php

use PHPUnit\Framework\TestCase;

class Zigra_I18nTest extends TestCase
{
    private array $availableLangs;

    protected function setUp(): void
    {
        parent::setUp();
        // Simuliamo le lingue disponibili (caricate dai file .nls.php)
        $this->availableLangs = ['en_US', 'it_IT', 'fr_FR'];
        $_SESSION = []; // Reset della sessione per ogni test
    }

    public function testGetBrowserLanguages()
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'it-IT,en-US;q=0.8,fr;q=0.6';

        $expected = [
            'it-IT' => 1,
            'en-US' => 0.8,
            'fr' => 0.6,
        ];

        $result = Zigra_I18n::getBrowserLanguages();
        $this->assertSame($expected, $result);
    }

    public function testGetBrowserLanguagesNoHeader()
    {
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $result = Zigra_I18n::getBrowserLanguages();
        $this->assertEmpty($result);
    }

    public function testDetectBrowserLanguageWithMatch()
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'it-IT,en-US;q=0.8';

        $lang = Zigra_I18n::detectBrowserLanguage();
        $this->assertInstanceOf(Zigra_I18n_Nls::class, $lang);
        $this->assertSame('it_IT', $lang->key());
    }

    public function testDetectBrowserLanguageWithoutMatch()
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'sv-SE,ko-KR;q=0.8'; // Lingue reali ma non presenti nei file nls

        $lang = Zigra_I18n::detectBrowserLanguage();
        $this->assertFalse($lang, 'Expected false for languages not supported in nls files');
    }

    public function testMatchLangExact()
    {
        $lang = Zigra_I18n::matchLang('fr_FR');

        $this->assertInstanceOf(Zigra_I18n_Nls::class, $lang);
        $this->assertSame('fr_FR', $lang->key());
    }

    public function testMatchLangNoMatch()
    {
        $lang = Zigra_I18n::matchLang('xx_XX');

        $this->assertFalse($lang);
    }

    public function testGetLanguageFromUri()
    {
        $_SERVER['REQUEST_URI'] = '/it/';
        $result = Zigra_I18n::getLanguage($this->availableLangs, 'en_US');

        $this->assertInstanceOf(Zigra_I18n_Nls::class, $result);
        $this->assertSame('it_IT', $result->key());
    }

    public function testGetLanguageFromSession()
    {
        $_SESSION['language'] = 'fr_FR';
        $_SERVER['REQUEST_URI'] = '/';

        $result = Zigra_I18n::getLanguage($this->availableLangs, 'en_US');

        $this->assertInstanceOf(Zigra_I18n_Nls::class, $result);
        $this->assertSame('fr_FR', $result->key());
    }

    public function testGetLanguageDefaultsToBrowser()
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'it-IT,en-US;q=0.8';
        $_SERVER['REQUEST_URI'] = '/';

        $result = Zigra_I18n::getLanguage($this->availableLangs, 'en_US');

        $this->assertInstanceOf(Zigra_I18n_Nls::class, $result);
        $this->assertSame('it_IT', $result->key());
    }

    public function testGetLanguageDefaultsToFallback()
    {
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $_SERVER['REQUEST_URI'] = '/';
        $_SESSION = []; // Simuliamo l'assenza di sessione

        $result = Zigra_I18n::getLanguage($this->availableLangs, 'en_US');

        $this->assertInstanceOf(Zigra_I18n_Nls::class, $result);
        $this->assertSame('en_US', $result->key());
    }
}
