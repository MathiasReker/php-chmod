<?php

declare(strict_types=1);

$header = <<<'EOF'
    This file is part of the php-chmod package.
    (c) Mathias Reker <github@reker.dk>
    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.
    EOF;

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->in([__DIR__]);

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        'header_comment' => [
            'header' => $header,
            'comment_type' => 'PHPDoc',
            'location' => 'after_open',
            'separate' => 'bottom'
        ],
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'array_indentation' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                'expectedDeprecation'
            ]
        ],
        'use_arrow_functions' => true,
        'control_structure_braces' => true,
        'control_structure_continuation_position' => true,
        'self_static_accessor' => true,
        'ordered_interfaces' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'return_assignment' => true,
        'no_useless_else' => true,
        'no_superfluous_elseif' => true,
        'no_useless_return' => true,
        'multiline_comment_opening_closing' => true,
        'no_null_property_initialization' => true,
        'operator_linebreak' => true,
        'method_chaining_indentation' => true,
        'strict_param' => true,
        'strict_comparison' => true,
    ])
    ->setFinder($finder)
    ->setLineEnding(PHP_EOL);

return $config;
