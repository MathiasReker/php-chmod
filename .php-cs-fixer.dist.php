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
            'separate' => 'bottom',
        ],

        // Migration Rules
        '@PHP8x1Migration' => true,
        '@PHP8x1Migration:risky' => true,
        '@PHPUnit10x0Migration:risky' => true,

        // Doctrine Rules
        '@DoctrineAnnotation' => true,

        // In no presets
        'attribute_empty_parentheses' => true,
        'heredoc_closing_marker' => true,
        'multiline_string_to_heredoc' => true,
        'numeric_literal_separator' => true,
        'ordered_attributes' => true,
        'php_unit_attributes' => true,
        'return_to_yield_from' => true,
        'phpdoc_tag_casing' => true,
        'phpdoc_param_order' => true,
        'mb_str_functions' => true,
        'date_time_immutable' => true,
        'ordered_interfaces' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => ['expectedDeprecation'],
        ],
        'phpdoc_array_type' => true,
        'phpdoc_list_type' => true,
        'general_attribute_remove' => true,
        'multiline_promoted_properties' => true,
        'new_expression_parentheses' => true,
        'no_useless_printf' => true,

        // PER-CS3.0 Rules
        '@PER-CS3x0:risky' => true,

        // PHP-CS-Fixer Rules
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,

        // Symfony Rules
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'concat_space' => ['spacing' => 'one'],

        // Disable rule causing issues
        'multiline_whitespace_before_semicolons' => true,
    ])
    ->setFinder($finder)
    ->setLineEnding(PHP_EOL);

return $config;
