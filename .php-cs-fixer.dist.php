<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'application/cache',
        'application/logs',
        'application/session',
        'asset',
        'system',
    ])
    ->notPath([
        'index.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        'echo_tag_syntax' => ['format' => 'short'],
    ])
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setFinder($finder)
;
