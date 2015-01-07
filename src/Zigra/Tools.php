<?php

class Zigra_Tools
{
    public static function br2nl($string)
    {
        $return = preg_replace('#<br[[:space:]]*/?' . '[[:space:]]*>#i', chr(13) . chr(10), $string);
        return $return;
    }
}
