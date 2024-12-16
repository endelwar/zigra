<?php

class Zigra_Route_Compiler implements Zigra_Route_CompilerInterface
{
    public function compile(Zigra_Route $route): array
    {
        $pattern = $route->getPattern();
        $requirements = $route->getRequirements();
        // $options = $route->getOptions();
        $defaults = $route->getDefaults();
        $tokens = [];
        $variables = [];

        preg_match_all('@\{([\w\d\=_-]+)\}@', (string)$pattern, $matches, \PREG_OFFSET_CAPTURE | \PREG_SET_ORDER);
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
        $tokens[] = [
            'pattern' => $pattern,
            'regex' => $regex,
            'variables' => $variables,
            'defaults' => $defaults,
        ];

        return $tokens;
    }
}
