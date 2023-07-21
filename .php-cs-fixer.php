<?php
declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

/*
 * exit code list
 *
 * 0 - OK.
 * 1 - General error (or PHP minimal requirement not matched).
 * 4 - Some files have invalid syntax (only in dry-run mode).
 * 8 - Some files need fixing (only in dry-run mode).
 * 16 - Configuration error of the application.
 * 32 - Configuration error of a Fixer.
 * 64 - Exception raised within the application.
 */
return (new Config())->setRules([
    '@PhpCsFixer'       => true,
    'no_unused_imports' => true,
    'dir_constant'      => true,
    'ereg_to_preg'      => true,
    'no_unset_cast'     => false,
    'echo_tag_syntax'   => ['format' => 'long'],

    'void_return'                => true,
    'strict_param'               => true,
    'static_lambda'              => false,
    'phpdoc_summary'             => false,
    'strict_comparison'          => true,
    'single_line_throw'          => true,
    'heredoc_indentation'        => true,
    'protected_to_private'       => false,
    'combine_nested_dirname'     => true,
    'ternary_to_null_coalescing' => true,
    'no_superfluous_phpdoc_tags' => false,

    'declare_parentheses'                     => true,
    'statement_indentation'                   => true,
    'control_structure_braces'                => true,
    'single_space_around_construct'           => true,
    'no_multiple_statements_per_line'         => true,
    'control_structure_continuation_position' => ['position' => 'next_line'],

    'not_operator_with_space'                          => false,
    'php_unit_test_class_requires_covers'              => false,
    'nullable_type_declaration_for_default_null_value' => true,

    'phpdoc_line_span'                    => true,
    'phpdoc_to_param_type'                => true,
    'phpdoc_to_return_type'               => true,
    'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],

    'phpdoc_separation'                   => ['groups' => [
        ['deprecated', 'link', 'see', 'since'],
        ['author', 'change', 'copyright', 'license'],
        ['category', 'package', 'subpackage'],
        ['property', 'property-read', 'property-write'],
        ['param'],
        ['return', 'throws'],
    ]],

    'list_syntax'                            => ['syntax' => 'short'],
    'array_syntax'                           => ['syntax' => 'short'],
    'error_suppression'                      => ['noise_remaining_usages' => true],
    'method_argument_space'                  => ['on_multiline' => 'ensure_fully_multiline'],
    'declare_equal_normalize'                => ['space' => 'single'],
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],

    'no_extra_blank_lines' => [
        'tokens' => [
            'extra',
            'break',
            'continue',
        ],
    ],

    'yoda_style' => [
        'equal'            => false,
        'identical'        => false,
        'less_and_greater' => false,
    ],

    'binary_operator_spaces' => [
        'default' => 'align_single_space_minimal',
    ],

    'global_namespace_import' => [
        'import_classes'   => true,
        'import_constants' => true,
        'import_functions' => true,
    ],

    'ordered_imports' => [
        'sort_algorithm' => 'length',
        'imports_order'  => ['const', 'function', 'class'],
    ],

    'curly_braces_position' => [
        'anonymous_classes_opening_brace'           => 'next_line_unless_newline_at_signature_end',
        'control_structures_opening_brace'          => 'next_line_unless_newline_at_signature_end',
        'anonymous_functions_opening_brace'         => 'next_line_unless_newline_at_signature_end',
        'allow_single_line_anonymous_functions'     => true,
        'allow_single_line_empty_anonymous_classes' => true,
    ],

    'native_function_invocation' => [
        'scope'   => 'all',
        'strict'  => true,
        'include' => ['@all'],
        'exclude' => [
            'ApiName',
            'ApiPerms',
            'ApiRequest',
            'ApiResponse',
            'GoFunc',
            'GoEvent',
            'GoInterface',
            'Attribute',
        ],
    ],

    'blank_line_before_statement' => [
        'statements' => [
            'case',
            'declare',
            'default',
            'do',
            'for',
            'foreach',
            'goto',
            'if',
            'return',
            'switch',
            'throw',
            'try',
            'while',
            'exit',
        ],
    ],
])
    ->setFinder(
        Finder::create()
            ->in(__DIR__)
            ->files()
            ->name('/\.php$/')
            ->ignoreDotFiles(true)
            ->exclude('vendor')
    );
