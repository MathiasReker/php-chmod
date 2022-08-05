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
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'array_indentation' => true,
        'general_phpdoc_annotation_remove' => ['annotations' => ['expectedDeprecation']],
        'header_comment' => ['header' => $header, 'comment_type' => 'PHPDoc', 'location' => 'after_open', 'separate' => 'bottom'],
        'use_arrow_functions' => true,
    ])
    ->setFinder($finder)
    ->setLineEnding(PHP_EOL);

return $config;
