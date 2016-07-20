<?php

class Zigra_Exception extends Exception
{
    /**
     * @param string $code
     * @param string $message
     */
    public static function renderError($code, $message)
    {
        if (!self::isCommandLineInterface()) {
            self::renderHtmlHeader();
        }
        echo $code . ': ' . utf8_encode($message);
        if (!self::isCommandLineInterface()) {
            self::renderHtmlFooter();
        }
    }

    private static function renderHtmlHeader()
    {
        echo '<!DOCTYPE html><html><head><style type="text/css">body {font-size:20px;background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAVCAIAAAAmdTLBAAACUklEQVQYGQXBCQHAQAgDsPk3CaXfyVjyjcZRs+GzT41vX8nsociGod3TmuzZSwrd3BvhO1NuRpMgc8vdVE49Dl0ucM7cdt/tLQIeAKePn9xuBoCzYghrg3kU8A7PvUNlv/MZyc3Bp0O9+GivB+wUvVFfGeF0AYYbgivldGd1H55ufcdkk28X4snv7oo8OUs2volUdqEHXY0OwVPWqFsl5Oce5Y5OVC7bmwbOI6zrW++W6SZGTeuaHD3e6rNnsdI9+hoJLwwL6saiF1hf3XnaCe5l92VXy8knxT54NQnu+nzWe6v4HTm0lYNzjsYKFHHlrYSPHYKUCi9IX5Bgt8oAwhnLtZyx6axmxxzPBP00cMFbS4D8jneAcxtmWlwtrQR3lIJLnoB7h/loVxyo9WDfxOJyPF1LMtLd56oUGRJRMobWyBcrF+9yUQh8RC4HPpnbo4zKRy2MFccNHJlBv1M5uFUP9U4I0HzXkXJWrKK83T5qHfARy1t5+fF60LLqLvoawFczYDEgsFq+YsC5kS+bmVrk9UPg6sGbedXORedl3+69i24JmrsuuFJ0thTw8frVUncxQ/vZsjdZsncL7N2zz8Zu2rMfoHYB360+mqseeCPLwmstnnKbtiuXM2kE3shPE+KQxMNPGJYigdKDd6BqY2X7KGvC3O4R0G6ws6CvbPeDqx0w7fKMXgUvy3uACSuZqybxDvZykPzyNJNvYXuhoX2vm6urrEPvtaVod6tmAiFBNjtcFO8bUo/s9HGAxrN+fb7Zd8YuIgs3W1noIyMqmebyA1wMl6BkwhCmAAAAAElFTkSuQmCC);} div.main {width:60%;padding:20px;margin:50px auto 0;background-color:#fff;border-radius:15px;}</style></head><body><div class="main">';
    }

    private static function renderHtmlFooter()
    {
        echo '</div></body></html>';
    }

    public static function isCommandLineInterface()
    {
        return (php_sapi_name() === 'cli');
    }

    /**
     * @param Exception $e
     */
    public static function displayException(Exception $e)
    {
        echo $e->getCode();
        echo '<hr>';
        echo $e->getMessage();
        echo '<hr>';
        echo $e->getLine();
        echo '<hr>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
}
