<?php

declare(strict_types=1);

namespace Zigra\Route;

use Zigra\Route;

interface CompilerInterface
{
    public function compile(Route $route);
}
