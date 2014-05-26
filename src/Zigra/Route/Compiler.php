<?php

class Zigra_Route_Compiler
{

    //'#\/tellme\/users\/[\w\d_]+\/[\w\d_]+#'
    public function Compile(Zigra_Route $route)
    {
        $pattern = $route->GetPattern();
        $requirements = $route->GetRequirements();
        //$options = $route->GetOptions();
        $defaults = $route->GetDefaults();
        //$len = strlen($pattern);
        $tokens = array();
        $variables = array();
        $pos = 0;
        $regex = '#';

        preg_match_all('#.\{([\w\d_-]+)\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        if (count($matches)) {
            foreach ($matches as $match) {
                //var_dump($match);
                if ($text = substr($pattern, $pos, $match[0][1] - $pos)) {
                    $regex .= str_replace('/', '\/', $text) . '\/';
                }
                if ($var = $match[1][0]) {
                    if (isset($requirements[$var])) {
                        $regex .= '(' . $requirements[$var] . ')\/';
                    } else {
                        //$regex .= '([\w\d_]+)\/';
                        $regex .= '(?P<' . $var . '>[\w\d_-]+)\/';
                    }
                    $variables[] = $match[1][0];
                }
                $pos = $match[0][1] + strlen($match[0][0]);
            }
            $regex = rtrim($regex, '\/') . '$#x';
        } else {
            $regex .= str_replace('/', '\/', $pattern) . '$#x';
        }

        $tokens[] = array(
            'pattern' => $pattern,
            'regex' => $regex,
            'variables' => $variables,
            'defaults' => $defaults,

        );
        return $tokens;
    }
}
