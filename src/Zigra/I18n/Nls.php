<?php

/**
 * Class Zigra_I18n_Nls.
 *
 * @author Manuel Dalla Lana <endelwar@aregar.it>
 */
class Zigra_I18n_Nls
{
    protected string $isocode;

    protected string $locale;

    protected string $language;

    protected string $fullname;

    protected string $encoding;

    /** @var array|string */
    protected $aliases;

    protected string $display;

    protected string $key;

    protected string $direction;

    protected string $regionsubtag;

    /**
     * Initialize Zigra_I18n_Nls object.
     *
     * @param string|null $lang language iso code
     */
    public static function init(string $lang = null): ?self
    {
        if (null === $lang) {
            return new self();
        }
        $lang_file = __DIR__ . \DIRECTORY_SEPARATOR . 'nls' . \DIRECTORY_SEPARATOR . $lang . '.nls.php';

        if (file($lang_file)) {
            $nls = include $lang_file;
            $obj = self::fromArray($nls);
            unset($nls);

            return $obj;
        }

        throw new \RuntimeException('Cannot load language file "' . $lang_file . '"', \E_ERROR);
    }

    /**
     * Load language information from data array.
     */
    public static function fromArray(array $data): self
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
            if (isset($t[0])) {
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
            exit();
        }

        return $obj;
    }

    public function matches(string $str): bool
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
            for ($i = 0; $i < $aliases_nr; ++$i) {
                if (strtolower($aliases[$i]) === strtolower($str)) {
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
