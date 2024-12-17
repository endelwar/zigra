<?php

declare(strict_types=1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Access to constant USERTYPE on an unknown class Doctrine_Record\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/src/Zigra/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to property \\$id on an unknown class Doctrine_Record\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/src/Zigra/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method toArray\\(\\) on an unknown class Doctrine_Record\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/src/Zigra/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\$user of method Zigra_User\\:\\:setAsLoggedIn\\(\\) has invalid type Doctrine_Record\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/src/Zigra/User.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
