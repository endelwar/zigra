<?php

/**
 * Class Zigra_I18n_Nls
 *
 * @author Manuel Dalla Lana <endelwar@aregar.it>
 */
class Zigra_I18n_Nls
{
    /** @var string $isocode */
    protected $isocode;
    /** @var string $locale */
    protected $locale;
    /** @var string $fullname */
    protected $fullname;
    /** @var string $encoding */
    protected $encoding;
    /** @var array|string $aliases */
    protected $aliases;
    /** @var string $display */
    protected $display;
    /** @var string $key */
    protected $key;
    /** @var string $direction */
    protected $direction;

    /**
     * Initialize Zigra_I18n_Nls object
     *
     * @param string $lang language iso code
     *
     * @return Zigra_I18n_Nls|null
     */
    public static function init($lang = null)
    {
        if (null === $lang) {
            $obj = new self();

            return $obj;
        }
        $lang_file = __DIR__ . DIRECTORY_SEPARATOR . 'nls' . DIRECTORY_SEPARATOR . $lang . '.nls.php';

        if (file($lang_file)) {
            $nls = include $lang_file;
            $obj = self::fromArray($nls);
            unset($nls);

            return $obj;
        }
        trigger_error('Cannot load language file "' . $lang_file . '"', E_ERROR);

        return null;
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
        $obj = new self();

        // name and key
        if (isset($data['englishlang'])) {
            foreach ($data['englishlang'] as $k => $v) {
                $obj->fullname = $v;
                $obj->key = $k;
                break;
            }
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

        $fields = ['language', 'locale', 'encoding', 'direction'];
        foreach ($fields as $field) {
            // get the field value
            if (isset($data[$field][$obj->key])) {
                $obj->$field = $data[$field][$obj->key];
            }
        }

        // get aliases
        if (isset($data['alias'])) {
            $obj->aliases = array_keys($data['alias']);
        }

        if ($obj->key === '') {
            trigger_error('Big Problems dude! $key in not where it should');
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
        if (
            ($str === $this->name()) ||
            ($str === $this->isocode()) ||
            ($str === $this->fullname())
        ) {
            return true;
        }
        $aliases = $this->aliases();

        if (is_array($aliases) && count($aliases)) {
            $aliases_nr = count($aliases);
            for ($i = 0; $i < $aliases_nr; $i++) {
                if (strtolower($aliases[$i]) === strtolower($str)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
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
     * @return null|string
     */
    public function display()
    {
        if (!$this->display) {
            return null;
        }

        return $this->display;
    }

    /**
     * @return string
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
     * @return null|string
     */
    public function fullname()
    {
        if (!$this->fullname) {
            return null;
        }

        return $this->fullname;
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

        return [];
    }

    /**
     * @return string
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
