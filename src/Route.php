<?php

declare(strict_types=1);

namespace Zigra;

use InvalidArgumentException;
use Zigra\Route\Compiler;
use Zigra\Route\CompilerInterface;

class Route
{
    protected $pattern;
    protected $defaults;
    protected $requirements;
    protected $compiledRoute;
    protected $compilerClass;

    /**
     * @param string $pattern
     */
    public function __construct(
        $pattern,
        array $defaults = [],
        array $requirements = [],
        string $compilerClass = null
    ) {
        $this->setPattern($pattern);
        $this->setDefaults($defaults);
        $this->setRequirements($requirements);
        // Set a default compiler class if none is passed
        $this->compilerClass = $compilerClass ?? Compiler::class;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern): void
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

    /**
     * @param string|null $compilerClass
     */
    public function setCompilerClass(string $compilerClass): void
    {
        if (!class_exists($compilerClass)) {
            throw new Exception('Route Compiler class does not exists');
        }

        if (!is_subclass_of($compilerClass, \Zigra\Route\CompilerInterface::class)) {
            throw new Exception(sprintf('%s does not implement Zigra\\Route\\CompilerInterface', $compilerClass));
        }

        $this->compilerClass = $compilerClass;
    }

    public function getCompilerClass(): string
    {
        return $this->compilerClass;
    }

    /**
     * @param string $regex
     */
    private function sanitizeRequirement($regex): string
    {
        if ('^' === $regex[0]) {
            $regex = mb_substr($regex, 1);
        }

        if ('$' === mb_substr($regex, -1)) {
            $regex = mb_substr($regex, 0, -1);
        }

        return $regex;
    }

    public function compile()
    {
        if (null !== $this->compiledRoute) {
            return $this->compiledRoute;
        }

        $className = $this->getCompilerClass();
        /** @var CompilerInterface $routeCompiler */
        $routeCompiler = new $className();
        $this->compiledRoute = $routeCompiler->compile($this);

        return $this->compiledRoute;
    }

    /**
     * Create url for given arguments.
     *
     * @param array $params argument values for url parameters in this route
     *
     * @return string $url generated url
     *
     * @throws InvalidArgumentException if required params are missing
     */
    public function generate($params): string
    {
        $compiledRoute = $this->compile();

        if (count($params) > 0 && 0 === (is_array($compiledRoute[0]['variables']) || $compiledRoute[0]['variables'] instanceof \Countable ? count($compiledRoute[0]['variables']) : 0)) {
            throw new InvalidArgumentException('Zigra_Route->generate: this route doesn\'t have parameters');
        }

        if ((is_array($compiledRoute[0]['variables']) || $compiledRoute[0]['variables'] instanceof \Countable ? count($compiledRoute[0]['variables']) : 0) !== count($params)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Zigra_Route->generate: missing %d parameters',
                    (is_array($compiledRoute[0]['variables']) || $compiledRoute[0]['variables'] instanceof \Countable ? count($compiledRoute[0]['variables']) : 0) - count($params)
                )
            );
        }

        // right quantity of parameters, let's verify that they are the right ones
        $paramdiff = array_diff_key(array_flip($compiledRoute['variables']), $params);
        if (!empty($paramdiff)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Zigra_Route->generate: wrong parameters name, missing: %s',
                    implode(', ', array_flip($paramdiff))
                )
            );
        }
        $parameters = [];
        $values = [];
        foreach ($params as $key => $value) {
            $parameters[] = '{' . $key . '}';
            $values[] = $value;
        }

        return str_replace($parameters, $values, $compiledRoute['pattern']);
    }
}
