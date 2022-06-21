<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

// Create finder instance containing which files to cs fix
$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new Config)
    ->setFinder($finder)
    ->setRiskyAllowed(false)
    ->setHideProgress(false)
    ->setUsingCache(true)
    ->setRules([
        '@PSR2'                               => true,
        'no_blank_lines_after_phpdoc'         => true,
        'no_empty_phpdoc'                     => true,
        'no_unused_imports'                   => true,
        'no_blank_lines_after_class_opening'  => true,
        'no_whitespace_in_blank_line'         => true,
        'not_operator_with_space'             => true,
        'phpdoc_indent'                       => true,
        'phpdoc_scalar'                       => true,
        'phpdoc_trim'                         => true,
        'phpdoc_separation'                   => true,
        'phpdoc_no_useless_inheritdoc'        => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_order'                        => true,
        'trailing_comma_in_multiline'         => true,
        'object_operator_without_whitespace'  => true,
        'full_opening_tag'                    => true,
        'whitespace_after_comma_in_array'     => true,
        'single_quote'                        => true,

        'return_type_declaration'     => ['space_before' => 'none'],
        'array_syntax'                => ['syntax' => 'short'],
        'no_extra_blank_lines'        => ['tokens' => ['extra']],
        'concat_space'                => ['spacing' => 'one'],
        'ordered_imports'             => ['sort_algorithm' => 'length'],
        'binary_operator_spaces'      => ['operators' => ['=>' => 'align_single_space_minimal']],
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'blank_line_before_statement' => ['statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try', 'for', 'foreach', 'if', 'switch', 'do', 'while']],

        'method_argument_space' => [
            'keep_multiple_spaces_after_comma' => false,
            'on_multiline'                     => 'ignore',
            'after_heredoc'                    => true,
        ],
        'echo_tag_syntax'       => [
            'format'                         => 'short',
            'shorten_simple_statements_only' => false,
        ],
    ]);
