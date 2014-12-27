<?php

class Zigra_Route_Compiler
{
    public function Compile(Zigra_Route $route)
    {
        $pattern = $route->GetPattern();
        $requirements = $route->GetRequirements();
        //$options = $route->GetOptions();
        $defaults = $route->GetDefaults();
        $tokens = array();
        $variables = array();

        preg_match_all('#\{([\w\d\=_-]+)\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $regex = str_replace('/', '\/', $pattern);
        if (count($matches)) {
            // named variables found
            foreach ($matches as $match) {
                $var = $match[1][0];
                $requirement = '[^\/]+';
                if (isset($requirements[$var])) {
                    $requirement = $requirements[$var];
                }
                $variables[] = $match[1][0];
                $regex = str_replace($match[0][0], '(?P<' . $var . '>' . $requirement . ')', $regex);
            }
        }
        $regex = '#^' . $regex . '$#x';
        $tokens[] = array(
            'pattern' => $pattern,
            'regex' => $regex,
            'variables' => $variables,
            'defaults' => $defaults
        );

        return $tokens;
    }
}
