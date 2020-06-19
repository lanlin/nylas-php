<?php

/**
 * 退出码列表
 *
 * 0 - OK.
 * 1 - General error (or PHP minimal requirement not matched).
 * 4 - Some files have invalid syntax (only in dry-run mode).
 * 8 - Some files need fixing (only in dry-run mode).
 * 16 - Configuration error of the application.
 * 32 - Configuration error of a Fixer.
 * 64 - Exception raised within the application.
 */
return \PhpCsFixer\Config::create()->setRules([
    '@PhpCsFixer' => true,

    'dir_constant' => true,
    'ereg_to_preg' => true,
    'no_unset_cast' => false,
    'no_short_echo_tag' => false,

    'void_return' => true,
    'strict_param' => true,
    'static_lambda'  => false,
    'phpdoc_summary' => false,
    'strict_comparison' => true,
    'single_line_throw' => true,
    'heredoc_indentation' => true,
    'protected_to_private' => false,
    'combine_nested_dirname' => true,
    'ternary_to_null_coalescing' => true,
    'no_superfluous_phpdoc_tags' => false,

    'phpdoc_line_span' => true,
    'phpdoc_to_param_type' => true,
    'phpdoc_to_return_type' => true,
    'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],

    'not_operator_with_space' => false,
    'php_unit_test_class_requires_covers' => false,
    'nullable_type_declaration_for_default_null_value' => true,

    'list_syntax' => ['syntax' => 'short'],
    'array_syntax' => ['syntax' => 'short'],

    'error_suppression' => ['noise_remaining_usages' => true],

    'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],

    'declare_equal_normalize' => ['space' => 'single'],

    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],

     'no_extra_blank_lines' => ['tokens' =>
     [
         'extra', 'break', 'continue',
     ]],

    'native_function_invocation' =>
    [
        'include' => ['@all'],
        'scope' => 'all',
        'strict' => true,
    ],

    'binary_operator_spaces' =>
    [
        'operators' => ['===' => 'align_single_space_minimal'],
        'default'   => 'align',
    ],

    'braces' =>
    [
        'allow_single_line_closure' => true,
        'position_after_control_structures' => 'next',
        'position_after_anonymous_constructs' => 'next',
        'position_after_functions_and_oop_constructs' => 'next',
    ],

    'global_namespace_import' =>
    [
        'import_classes' => true,
        'import_constants' => false,
        'import_functions' => false,
    ],

    'ordered_imports' =>
    [
        'sort_algorithm' => 'length',
        'imports_order'  => ['const', 'function', 'class'],
    ],

    'blank_line_before_statement' => ['statements' =>
     [
         'case', 'declare', 'default', 'do', 'for', 'foreach', 'goto', 'if',
         'return', 'switch', 'throw', 'try', 'while', 'die', 'exit'
     ]],
])
->setFinder(\PhpCsFixer\Finder::create()
    ->exclude('verdor')
    ->in(__DIR__)
);
