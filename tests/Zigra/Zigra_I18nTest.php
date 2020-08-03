<?php

namespace ZigraTest;

use Zigra_I18n;
use Zigra_I18n_Nls;

class Zigra_I18nTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider langProvider
     * @param string $request_uri
     * @param array $available_languages
     * @param string $default_language
     */
    public function testGetLanguage($request_uri, $available_languages, $default_language)
    {
        $_SERVER['REQUEST_URI'] = $request_uri;
        $i18n = Zigra_I18n::getLanguage($available_languages, $default_language);
        $this->assertInstanceOf(Zigra_I18n_Nls::class, $i18n);
        $this->assertSame($default_language, $i18n->key());
    }

    public function langProvider()
    {
        return [
            ['/en/foo/bar', ['en_US', 'it_IT'], 'en_US'],
            ['/ja/foo/bar', ['en_US', 'it_IT'], 'en_US'],
            ['/ja/foo/bar', ['ja_JP', 'it_IT'], 'ja_JP'],
        ];
    }
}
