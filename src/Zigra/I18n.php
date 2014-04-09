<?php

class Zigra_I18n
{

    /**
     * @ignore
     */
    private static $_nls;

    /**
     * @ignore
     */
    private static function _loadNls()
    {
        if (!is_array(self::$_nls)) {
            self::$_nls = array();

            $nlsdir = __DIR__ . DIRECTORY_SEPARATOR . 'I18n' . DIRECTORY_SEPARATOR . 'nls';
            $files = glob($nlsdir . '/*nls.php');
            $nls = '';
            if (is_array($files) && count($files)) {
                for ($i = 0; $i < count($files); $i++) {
                    if (!is_file($files[$i])) {
                        continue;
                    }
                    $fn = basename($files[$i]);

                    unset($nls);
                    $nls = include $files[$i];
                    if (isset($nls)) {
                        $obj = Zigra_I18n_Nls::fromArray($nls);
                        unset($nls);
                        self::$_nls[$obj->key()] = $obj;
                    }
                }
            }
        }
    }

    /**
     * Return the list of languages understood by the current browser (if any).
     *
     * @return array of Strings representing the languages the browser supports.
     */
    public static function getBrowserLanguages()
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return false;
        }

        preg_match_all(
            '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            $lang_parse
        );
        $langs = array();
        if (count($lang_parse[1])) {
            // create a list like "en" => 0.8
            $langs = array_combine($lang_parse[1], $lang_parse[4]);

            // set default to 1 for any language without q factor
            foreach ($langs as $lang => $val) {
                if ($val === '') {
                    $langs[$lang] = 1;
                }
            }

            // sort list based on value
            arsort($langs, SORT_NUMERIC);
        }

        return $langs;
    }

    /**
     * Cross reference the browser preferred language with those
     * that are available (via NLS Files). To find the first suitable language.
     *
     * @return Zigra_I18n_Nls First suitable lang object or false.
     */
    public static function detectBrowserLanguage()
    {
        $langs = self::getBrowserLanguages();
        if (!is_array($langs) || !count($langs)) {
            return false;
        }

        self::_loadNls();

        foreach ($langs as $onelang => $weight) {
            if (isset(self::$_nls[$onelang])) {
                return self::$_nls[$onelang];
            }

            foreach (self::$_nls as $key => $obj) {
                if ($obj->matches($onelang)) {
                    //return $obj->name();
                    return $obj;
                }
            }
        }

        return false;
    }

    public static function matchLang($lang)
    {
        self::_loadNls();
        if (isset(self::$_nls[$lang])) {
            return self::$_nls[$lang];
        }
        foreach (self::$_nls as $key => $obj) {
            if ($obj->matches($lang)) {
                return $obj;
            }
        }
    }

    /**
     * get i18n object from guessing or session,
     *
     * @param string $defaultLang default language
     *
     * @return Zigra_I18n_Nls suitable lang object.
     */
    public static function getLanguage($defaultLang)
    {
        $regs = array();
        if (preg_match('%^/([a-z]{2})/?[\w-_/]*$%', $_SERVER['REQUEST_URI'], $regs)) {
            $result = $regs[1];
            $i18n = Zigra_I18n::matchLang($result);
            if (!$i18n) {
                $i18n = Zigra_I18n_Nls::init($defaultLang);
            } else {
                if (!in_array($i18n->key(), $availableLangs)) {
                    $i18n = Zigra_I18n_Nls::init($defaultLang);
                }
            }
            $_SESSION['language'] = $i18n->key();
        } else {
            if (isset($_SESSION['language'])) {
                $i18n = Zigra_I18n_Nls::init($_SESSION['language']);
            } else {
                $i18n = Zigra_I18n::detectBrowserLanguage();
                if (!$i18n) {
                    $i18n = Zigra_I18n_Nls::init($defaultLang);
                } else {
                    if (!in_array($i18n->key(), $availableLangs)) {
                        $i18n = Zigra_I18n_Nls::init($defaultLang);
                    }
                }

                $_SESSION['language'] = $i18n->key();
            }
        }
        return $i18n;
    }
}
