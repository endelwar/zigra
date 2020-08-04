<?php

declare(strict_types=1);

namespace Zigra\Route;

use Zigra\Route;

class Compiler implements CompilerInterface
{
    public function compile(Route $route): array
    {
        $pattern = $route->getPattern();
        $requirements = $route->getRequirements();
        $defaults = $route->getDefaults();
        $variables = [];

        preg_match_all('@\{([\w\=_-]+)\}@', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $regex = str_replace('/', '\/', $pattern);
        if (count($matches)) {
            // named variables found
            foreach ($matches as $match) {
                $var = $match[1][0];
                $requirement = $requirements[$var] ?? '[^\/]+';
                $variables[] = $match[1][0];
                $regex = str_replace($match[0][0], '(?P<' . $var . '>' . $requirement . ')', $regex);
            }
        }
        $regex = '@^' . $regex . '$@';

        return [
            'pattern' => $pattern,
            'regex' => $regex,
            'variables' => $variables,
            'defaults' => $defaults,
        ];
    }
}
