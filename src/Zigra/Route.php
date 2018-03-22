<?php

class Zigra_Route
{
    protected $pattern;
    protected $defaults;
    protected $requirements;
    public $options;
    protected $compiledRoute = false;

    /**
     * @param string $pattern
     * @param array $defaults
     * @param array $requirements
     * @param array $options
     */
    public function __construct(
        $pattern,
        array $defaults = [],
        array $requirements = [],
        array $options = []
    ) {
        $this->setPattern($pattern);
        $this->setDefaults($defaults);
        $this->setRequirements($requirements);
        $this->setOptions($options);
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = trim($pattern);

        if (empty($this->pattern) || $this->pattern[0] !== '/') {
            $this->pattern = '/' . $this->pattern;
        }
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param array $requirements
     */
    public function setRequirements(array $requirements)
    {
        $this->requirements = [];
        foreach ($requirements as $key => $value) {
            $this->requirements[$key] = $this->sanitizeRequirement($value);
        }
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge(
            ['compiler_class' => 'Zigra_Route_Compiler'],
            $options
        );
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * @param string $regex
     * @return string
     */
    private function sanitizeRequirement($regex)
    {
        if ($regex[0] === '^') {
            $regex = substr($regex, 1);
        }

        if (substr($regex, -1) === '$') {
            $regex = substr($regex, 0, -1);
        }

        return $regex;
    }

    public function compile()
    {
        if (!$this->compiledRoute) {
            $className = $this->getOption('compiler_class');
            $routeCompiler = new $className();

            $compiledRoute = $routeCompiler->compile($this);
        } else {
            $compiledRoute = $this->compiledRoute;
        }

        return $compiledRoute;
    }

    /**
     * Create url for given arguments.
     *
     * @param array $params argument values for url parameters in this route
     *
     * @throws InvalidArgumentException if required params are missing
     *
     * @return string $url generated url
     */
    public function generate($params)
    {
        $compiledRoute = $this->compile();

        if (count($params) > 0 && count($compiledRoute[0]['variables']) === 0) {
            throw new InvalidArgumentException('Zigra_Route->generate: this route doesn\'t have parameters');
        } elseif (count($compiledRoute[0]['variables']) !== count($params)) {
            throw new InvalidArgumentException(
                'Zigra_Route->generate: missing ' .
                count($compiledRoute[0]['variables']) - count($params) .
                ' parameters'
            );
        }
        // right number of parameters, let's verify that they are the right ones
        $paramdiff = array_diff_key(array_flip($compiledRoute[0]['variables']), $params);
        if (!empty($paramdiff)) {
            throw new InvalidArgumentException(
                    'Zigra_Route->generate: wrong parameters name, missing: ' .
                    implode(', ', array_flip($paramdiff))
                );
        }
        $parameters = [];
        $values = [];
        foreach ($params as $key => $value) {
            $parameters[] = '{' . $key . '}';
            $values[] = $value;
        }

        return str_replace($parameters, $values, $compiledRoute[0]['pattern']);
    }
}
