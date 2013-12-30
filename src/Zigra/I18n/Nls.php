<?php

/**
 * Class Zigra_I18n_Nls
 *
 * @author Manuel Dalla Lana <endelwar@aregar.it>
 *
 */
class Zigra_I18n_Nls
{
    protected $isocode;
    protected $locale;
    protected $fullname;
    protected $encoding;
    protected $aliases;
    protected $display;
    protected $key;
    protected $direction;

    /**
     * Initialize Zigra_I18n_Nls object
     *
     * @param string $lang language iso code
     *
     * @return Zigra_I18n_Nls
     */
    public static function init($lang = null)
    {
        if (is_null($lang)) {
            $obj = new Zigra_I18n_Nls();
        } else {
            $lang_file = __DIR__ . DIRECTORY_SEPARATOR . 'nls' . DIRECTORY_SEPARATOR . $lang . '.nls.php';

            if (file($lang_file)) {
                $nls = include $lang_file;
                $obj = Zigra_I18n_Nls::fromArray($nls);
                unset($nls);
            }
        }

        return $obj;
    }

    /**
     * Load language information from data array
     *
     * @param array $data
     *
     * @return Zigra_I18n_Nls
     */
    public static function fromArray($data)
    {
        $obj = new Zigra_I18n_Nls();

        // name and key
        if (isset($data['englishlang'])) {
            foreach ($data['englishlang'] as $k => $v) {
                $obj->fullname = $v;
                $obj->key = $k;
                break;
            }
        }

        // get the display value
        if (isset($data['language'][$obj->key])) {
            $obj->display = $data['language'][$obj->key];
        }

        // get the isocode? (ISO 639)
        if (isset($data['isocode'][$obj->key])) {
            $obj->isocode = $data['isocode'][$obj->key];
        } else {
            $t = explode('_', $obj->key);
            if (is_array($t) && count($t)) {
                $obj->isocode = $t[0];
            }
        }

        // get the locale
        if (isset($data['locale'][$obj->key])) {
            $obj->locale = $data['locale'][$obj->key];
        }

        // get the encoding
        if (isset($data['encoding'][$obj->key])) {
            $obj->encoding = $data['encoding'][$obj->key];
        }

        // get the direction
        if (isset($data['direction'][$obj->key])) {
            $obj->direction = $data['direction'][$obj->key];
        }

        // get aliases
        if (isset($data['alias'])) {
            $obj->aliases = array_keys($data['alias']);
        }

        if ($obj->key == '') {
            var_dump('Grave errore', $data, $obj);
            die();
        }
        return $obj;
    }

    /**
     * @param string $str
     *
     * @return bool
     */
    public function matches($str)
    {
        if ($str == $this->name()) {
            return true;
        }
        if ($str == $this->isocode()) {
            return true;
        }
        if ($str == $this->fullname()) {
            return true;
        }
        $aliases = $this->aliases();
        if (!is_array($aliases)) {
            $aliases = explode(',', $aliases);
        }
        if (is_array($aliases) && count($aliases)) {
            $aliases_nr = count($aliases);
            for ($i = 0; $i < $aliases_nr; $i++) {
                if (strtolower($aliases[$i]) == strtolower($str)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function isocode()
    {
        if (!$this->isocode) {
            return substr($this->fullname, 0, 2);
        }
        return $this->isocode;
    }

    /**
     * @return mixed
     */
    public function display()
    {
        if ($this->display) {
            return $this->display;
        }
    }

    /**
     * @return mixed
     */
    public function locale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function encoding()
    {
        if (!$this->encoding) {
            return 'UTF-8';
        }
        return $this->encoding;
    }

    /**
     * @return mixed
     */
    public function fullname()
    {
        if ($this->fullname) {
            return $this->fullname;
        }
    }

    /**
     * @return array
     */
    public function aliases()
    {
        if ($this->aliases) {
            if (is_array($this->aliases)) {
                return $this->aliases;
            }
            return explode(',', $this->aliases);
        }
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function direction()
    {
        if ($this->direction) {
            return $this->direction;
        }
        return 'ltr';
    }
}
