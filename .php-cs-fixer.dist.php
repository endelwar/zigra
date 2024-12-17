<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'build',
        'vendor',
        'var',
    ])
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'cast_spaces' => ['space' => 'none'],
        'native_function_invocation' => true,
        'no_superfluous_phpdoc_tags' => true,
        'fully_qualified_strict_types' => true,
    ]);
