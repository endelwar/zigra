<?php

declare(strict_types=1);

namespace Zigra;

class Version
{
    public const VERSION = '0.8.7';

    public function getVersion(): string
    {
        return self::VERSION;
    }
}
