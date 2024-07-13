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
        'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
        'binary_operator_spaces' => ['default' => 'single_space', 'operators' => ['=>' => 'single_space']],
    ])
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setFinder($finder)
;
