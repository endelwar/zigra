<?php

declare(strict_types=1);

namespace Zigra\I18n;

use const DIRECTORY_SEPARATOR;

class Nls
{
    /** @var string */
    protected $isocode;
    /** @var string */
    protected $locale;
    /** @var string */
    protected $fullname;
    /** @var string */
    protected $encoding;
    /** @var array|string */
    protected $aliases;
    /** @var string */
    protected $display;
    /** @var string */
    protected $key;
    /** @var string */
    protected $direction;
    /** @var string */
    protected $regionsubtag;

    /**
     * Initialize Zigra_I18n_Nls object.
     *
     * @param string|null $lang language iso code
     *
     * @return Nls|null
     */
    public static function init(string $lang = null): ?self
    {
        if (null === $lang) {
            return new self();
        }
        $lang_file = __DIR__ . DIRECTORY_SEPARATOR . 'Nls' . DIRECTORY_SEPARATOR . $lang . '.nls.php';

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
     * Load language information from data array.
     *
     * @param array $data
     */
    public static function fromArray($data): self
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

        // get language with region subtag
        if (isset($data['regionsubtag'])) {
            $obj->regionsubtag = $data['regionsubtag'];
        }

        if ('' === $obj->key) {
            trigger_error('Big Problems dude! $key in not where it should');
            die();
        }

        return $obj;
    }

    /**
     * @param string $str
     */
    public function matches($str): bool
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
            foreach ($aliases as $iValue) {
                if (strtolower($iValue) === strtolower($str)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function name(): string
    {
        return $this->key;
    }

    public function isocode(): string
    {
        if (!$this->isocode) {
            return substr($this->fullname, 0, 2);
        }

        return $this->isocode;
    }

    public function display(): ?string
    {
        if (!$this->display) {
            return null;
        }

        return $this->display;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function encoding(): string
    {
        if (!$this->encoding) {
            return 'UTF-8';
        }

        return $this->encoding;
    }

    public function fullname(): ?string
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

    public function key(): string
    {
        return $this->key;
    }

    public function direction(): string
    {
        if ($this->direction) {
            return $this->direction;
        }

        return 'ltr';
    }

    public function regionsubtag(): string
    {
        return $this->regionsubtag;
    }
}
