<?php

class Zigra_I18n
{
    /**
     * @ignore
     */
    private static $nls;

    /**
     * @ignore
     */
    private static function loadNls(): void
    {
        if (!is_array(self::$nls)) {
            self::$nls = [];

            $nlsdir = __DIR__ . \DIRECTORY_SEPARATOR . 'I18n' . \DIRECTORY_SEPARATOR . 'nls';
            $files = glob($nlsdir . '/*nls.php');
            $nls = '';
            $filecount = is_countable($files) ? count($files) : 0;
            if (is_array($files) && $filecount) {
                for ($i = 0; $i < $filecount; ++$i) {
                    if (!is_file($files[$i])) {
                        continue;
                    }

                    unset($nls);
                    $nls = include $files[$i];
                    if (null !== $nls) {
                        $obj = Zigra_I18n_Nls::fromArray($nls);
                        unset($nls);
                        self::$nls[$obj->key()] = $obj;
                    }
                }
            }
        }
    }

    /**
     * Return the list of languages understood by the current browser (if any).
     *
     * @return array Array of Strings representing the languages the browser supports
     */
    public static function getBrowserLanguages(): array
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return [];
        }

        preg_match_all(
            '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[\d]+))?/i',
            (string)$_SERVER['HTTP_ACCEPT_LANGUAGE'],
            $lang_parse
        );
        $langs = [];
        if (count($lang_parse[1]) > 0) {
            // create a list like "en" => 0.8
            $langs = array_combine($lang_parse[1], $lang_parse[4]);

            // set default to 1 for any language without q factor
            foreach ($langs as $lang => $val) {
                if ('' === $val) {
                    $langs[$lang] = 1;
                }
            }

            // sort list based on value
            arsort($langs, \SORT_NUMERIC);
        }

        return $langs;
    }

    /**
     * Cross-reference the browser preferred language with those
     * that are available (via NLS Files). To find the first suitable language.
     *
     * @return Zigra_I18n_Nls|false first suitable lang object or false
     */
    public static function detectBrowserLanguage()
    {
        $langs = self::getBrowserLanguages();
        if (count($langs) < 1) {
            return false;
        }

        self::loadNls();

        foreach ($langs as $onelang => $weight) {
            if (isset(self::$nls[$onelang])) {
                return self::$nls[$onelang];
            }

            /** @var Zigra_I18n_Nls $obj */
            foreach (self::$nls as $obj) {
                if ($obj->matches($onelang)) {
                    return $obj;
                }
            }
        }

        return false;
    }

    public static function matchLang(string $lang)
    {
        self::loadNls();
        if (isset(self::$nls[$lang])) {
            return self::$nls[$lang];
        }
        foreach (self::$nls as $obj) {
            if ($obj->matches($lang)) {
                return $obj;
            }
        }

        return false;
    }

    /**
     * get i18n object from guessing or session,.
     *
     * @param array  $availableLangs all available languages
     * @param string $defaultLang    default language
     *
     * @return Zigra_I18n_Nls suitable lang object
     */
    public static function getLanguage(array $availableLangs, string $defaultLang)
    {
        $regs = [];
        if (preg_match('%^/([a-z]{2})/?[\w\-_/]*$%', (string)$_SERVER['REQUEST_URI'], $regs)) {
            $result = $regs[1];
            $i18n = self::matchLang($result);
            if (!$i18n) {
                $i18n = Zigra_I18n_Nls::init($defaultLang);
            } elseif (!in_array($i18n->key(), $availableLangs, true)) {
                $i18n = Zigra_I18n_Nls::init($defaultLang);
            }
        } elseif (isset($_SESSION['language'])) {
            $i18n = Zigra_I18n_Nls::init($_SESSION['language']);
        } else {
            $i18n = self::detectBrowserLanguage();
            if (!$i18n) {
                $i18n = Zigra_I18n_Nls::init($defaultLang);
            } elseif (!in_array($i18n->key(), $availableLangs, true)) {
                $i18n = Zigra_I18n_Nls::init($defaultLang);
            }
        }
        $_SESSION['language'] = $i18n->key();

        return $i18n;
    }
}
