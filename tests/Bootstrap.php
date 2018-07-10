<?php

/*
 * This file is part of Zigra.
 *
 * (c) Manuel Dalla Lana <manuel@pepeverde.agency>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use PHPUnit\Framework\TestCase;

error_reporting(E_ALL);
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

require_once __DIR__ . '/../vendor/autoload.php';

// PHPUnit 6 introduced a breaking change that
// removed PHPUnit_Framework_TestCase as a base class,
// and replaced it with \PHPUnit\Framework\TestCase
if (!class_exists(\PHPUnit_Framework_TestCase::class) && class_exists(TestCase::class)) {
    class_alias(TestCase::class, \PHPUnit_Framework_TestCase::class);
}
