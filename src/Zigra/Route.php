<?php

class Zigra_Route
{

    private $_pattern;
    private $_defaults;
    private $_requirements;
    public $_options;
    private $_compiledRoute = false;

    public function __construct(
        $pattern,
        array $defaults = array(),
        array $requirements = array(),
        array $options = array()
    ) {
        $this->SetPattern($pattern);
        $this->SetDefaults($defaults);
        $this->SetRequirements($requirements);
        $this->SetOptions($options);
    }

    public function SetPattern($pattern)
    {
        $this->_pattern = trim($pattern);

        if ($this->_pattern[0] !== '/' || empty($this->_pattern)) {
            $this->_pattern = '/' . $this->_pattern;
        }
    }

    public function GetPattern()
    {
        return $this->_pattern;
    }

    public function SetDefaults(array $defaults)
    {
        $this->_defaults = $defaults;
    }

    public function GetDefaults()
    {
        return $this->_defaults;
    }

    public function SetRequirements(array $requirements)
    {
        $this->_requirements = array();
        foreach ($requirements as $key => $value) {
            $this->_requirements[$key] = $this->_SanitizeRequirement($key, $value);
        }
    }

    public function GetRequirements()
    {
        return $this->_requirements;
    }

    public function SetOptions(array $options)
    {
        $this->_options = array_merge(
            array('compiler_class' => 'Zigra_Route_Compiler'),
            $options
        );
    }

    public function GetOptions()
    {
        return $this->_options;
    }

    public function GetOption($name)
    {
        return isset($this->_options[$name]) ? $this->_options[$name] : null;
    }

    private function _SanitizeRequirement($key, $regex)
    {
        if ($regex[0] == '^') {
            $regex = substr($regex, 1);
        }

        if (substr($regex, -1) == '$') {
            $regex = substr($regex, 0, -1);
        }

        return $regex;
    }

    public function Compile()
    {
        if (!$this->_compiledRoute) {
            $className = $this->GetOption('compiler_class');
            $routeCompiler = new $className;

            $compiledRoute = array();
            $compiledRoute = $routeCompiler->Compile($this);

        } else {
            $compiledRoute = $this->_compiledRoute;
        }

        return $compiledRoute;
    }

    /**
     * Create url for given arguments.
     *
     * @param array $params Argument values for url parameters in this route.
     *
     * @throws InvalidArgumentException If required params are missing.
     *
     * @return string Url.
     */
    public function Generate($params)
    {
        $compiledRoute = $this->Compile();

        if (count($compiledRoute[0]['variables']) != count($params)) {
            throw new InvalidArgumentException('Zigra_Route->Generate: dai, passami qualche parametro in più, tipo ' . count(
                $compiledRoute[0]['variables']
            ) . ' in tutto!');
        } else {
            // il numero dei parametri è giusto, verifichiamo che siano quelli giusti
            $paramdiff = array_diff_key(array_flip($compiledRoute[0]['variables']), $params);
            if (!empty($paramdiff)) {
                throw new InvalidArgumentException('Zigra_Route->Generate: i nomi dei parametri non sono corretti, manca: ' . implode(
                    ', ',
                    array_flip($paramdiff)
                ));
            } else {
                $parameters = array();
                $values = array();
                foreach ($params as $key => $value) {
                    $parameters[] = '{' . $key . '}';
                    $values[] = $value;
                }
                $url = str_replace($parameters, $values, $compiledRoute[0]['pattern']);

                return $url;
            }

        }
    }

}
