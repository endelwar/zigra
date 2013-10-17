<?php

class Zigra_I18n_Nls
{

    protected $_isocode;
    protected $_locale;
    protected $_fullname;
    protected $_encoding;
    protected $_aliases;
    protected $_display;
    protected $_key;
    protected $_direction;

    public static function init($lang = false)
    {
        if ($lang) {
            $lang_file = __DIR__ . DIRECTORY_SEPARATOR . 'nls' . DIRECTORY_SEPARATOR . $lang . '.nls.php';

            if (file($lang_file)) {
                $nls = include $lang_file;
                $obj = Zigra_I18n_Nls::fromArray($nls);
                unset($nls);
            }
        } else {
            $obj = new Zigra_I18n_Nls();
        }

        return $obj;
    }

    public static function fromArray($data)
    {
        $obj = new Zigra_I18n_Nls();

        // name and key
        if (isset($data['englishlang'])) {
            foreach ($data['englishlang'] as $k => $v) {
                $obj->_fullname = $v;
                $obj->_key = $k;
                break;
            }
        }

        // get the display value
        if (isset($data['language'][$obj->_key])) {
            $obj->_display = $data['language'][$obj->_key];
        }

        // get the isocode? (ISO 639)
        if (isset($data['isocode'][$obj->_key])) {
            $obj->_isocode = $data['isocode'][$obj->_key];
        } else {
            $t = explode('_', $obj->_key);
            if (is_array($t) && count($t)) {
                $obj->_isocode = $t[0];
            }
        }

        // get the locale
        if (isset($data['locale'][$obj->_key])) {
            $obj->_locale = $data['locale'][$obj->_key];
        }

        // get the encoding
        if (isset($data['encoding'][$obj->_key])) {
            $obj->_encoding = $data['encoding'][$obj->_key];
        }

        // get the direction
        if (isset($data['direction'][$obj->_key])) {
            $obj->_direction = $data['direction'][$obj->_key];
        }

        // get aliases
        if (isset($data['alias'])) {
            $obj->_aliases = array_keys($data['alias']);
        }

        if ($obj->_key == '') {
            var_dump('Grave errore', $data, $obj);
            die();
        }
        return $obj;
    }

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

    public function name()
    {
        return $this->_key;
    }

    public function isocode()
    {
        if (!$this->_isocode) {
            return substr($this->_fullname, 0, 2);
        }
        return $this->_isocode;
    }

    public function display()
    {
        if ($this->_display) {
            return $this->_display;
        }
    }

    public function locale()
    {
        return $this->_locale;
    }

    public function encoding()
    {
        if (!$this->_encoding) {
            return 'UTF-8';
        }
        return $this->_encoding;
    }

    public function fullname()
    {
        if ($this->_fullname) {
            return $this->_fullname;
        }
    }

    public function aliases()
    {
        if ($this->_aliases) {
            if (is_array($this->_aliases)) {
                return $this->_aliases;
            }
            return explode(',', $this->_aliases);
        }
    }

    public function key()
    {
        return $this->_key;
    }

    public function direction()
    {
        if ($this->_direction) {
            return $this->_direction;
        }
        return 'ltr';
    }

}
