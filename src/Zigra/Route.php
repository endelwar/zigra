<?php

/**
 * Class Zigra_Route.
 */
class Zigra_Route
{
    protected $pattern;
    protected $defaults;
    protected $requirements;
    public $options;
    protected $compiledRoute;

    public function __construct(
        string $pattern,
        array $defaults = [],
        array $requirements = [],
        array $options = []
    ) {
        $this->setPattern($pattern);
        $this->setDefaults($defaults);
        $this->setRequirements($requirements);
        $this->setOptions($options);
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = trim($pattern);

        if (empty($this->pattern) || '/' !== $this->pattern[0]) {
            $this->pattern = '/' . $this->pattern;
        }
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function setDefaults(array $defaults): void
    {
        $this->defaults = $defaults;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function setRequirements(array $requirements): void
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

    public function setOptions(array $options): void
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

    public function getOption(string $name): ?string
    {
        return $this->options[$name] ?? null;
    }

    private function sanitizeRequirement(string $regex): string
    {
        if ('^' === $regex[0]) {
            $regex = mb_substr($regex, 1);
        }

        if ('$' === mb_substr($regex, -1)) {
            $regex = mb_substr($regex, 0, -1);
        }

        return $regex;
    }

    public function compile(): array
    {
        if (is_array($this->compiledRoute)) {
            return $this->compiledRoute;
        }

        $className = $this->getOption('compiler_class');
        /** @var Zigra_Route_CompilerInterface $routeCompiler */
        $routeCompiler = new $className();

        return $routeCompiler->compile($this);
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
    public function generate(array $params): string
    {
        $compiledRoute = $this->compile();

        if (count($params) > 0 && 0 === (is_countable($compiledRoute[0]['variables']) ? count($compiledRoute[0]['variables']) : 0)) {
            throw new InvalidArgumentException('Zigra_Route->generate: this route doesn\'t have parameters');
        }

        if ((is_countable($compiledRoute[0]['variables']) ? count($compiledRoute[0]['variables']) : 0) !== count($params)) {
            throw new InvalidArgumentException(sprintf('Zigra_Route->generate: missing %d parameters', (is_countable($compiledRoute[0]['variables']) ? count($compiledRoute[0]['variables']) : 0) - count($params)));
        }

        // right number of parameters, let's verify that they are the right ones
        $paramdiff = array_diff_key(array_flip($compiledRoute[0]['variables']), $params);
        if (!empty($paramdiff)) {
            throw new InvalidArgumentException(sprintf('Zigra_Route->generate: wrong parameters name, missing: %s', implode(', ', array_flip($paramdiff))));
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
