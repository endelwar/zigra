<?php

declare(strict_types=1);

namespace ZigraTest;

use PHPUnit\Framework\TestCase;
use Zigra\I18n;
use Zigra\I18n\Nls;

class I18nTest extends TestCase
{
    /**
     * @dataProvider langProvider
     *
     * @param string $request_uri
     * @param array  $available_languages
     * @param string $default_language
     */
    public function testGetLanguage($request_uri, $available_languages, $default_language): void
    {
        $_SERVER['REQUEST_URI'] = $request_uri;
        $i18n = I18n::getLanguage($available_languages, $default_language);
        self::assertInstanceOf(Nls::class, $i18n);
        self::assertSame($default_language, $i18n->key());
    }

    public function langProvider(): array
    {
        return [
            ['/en/foo/bar', ['en_US', 'it_IT'], 'en_US'],
            ['/ja/foo/bar', ['en_US', 'it_IT'], 'en_US'],
            ['/ja/foo/bar', ['ja_JP', 'it_IT'], 'ja_JP'],
        ];
    }
}
