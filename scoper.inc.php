<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => "Polytranslate\Vendors",

    'finders' => [

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ])
            ->in('vendor'),

        Finder::create()->append([
            'composer.json',
            'composer.lock'
        ]),

    ],

    'exclude-files' => [],

    'patchers' => [],

    'exclude-namespaces' => [],
    'exclude-classes' => [],
    'exclude-functions' => [],
    'exclude-constants' => [],

    'expose-global-constants' => true,
    'expose-global-classes' => true,
    'expose-global-functions' => false,
    'expose-namespaces' => [],
    'expose-classes' => [],
    'expose-functions' => [
        'app',
        'imageavif',
        'findTranslationFiles',
        'calculateTranslationStatus',
        'printTranslationStatus',
        'extractLocaleFromFilePath',
        'extractTranslationKeys',
        'findTransUnitMismatches',
        'isTranslationCompleted',
        'printTitle',
        'printTable',
        'textColorRed',
        'textColorGreen',
        'textColorNormal',
        'trigger_deprecation',
        'litespeed_finish_request',
        'setproctitle',
        'append_config',
        'blank',
        'class_basename',
        'class_uses_recursive',
        'trait_uses_recursive',
        'e',
        'env',
        'filled',
        'object_get',
        'value',
        'optional',
        'preg_replace_array',
        'retry',
        'tap',
        'throw_if',
        'throw_unless',
        'transform',
        'windows_os',
        'with',
        'collect',
        'data_fill',
        'data_get',
        'data_set',
        'head',
        'value',
        'last',
    ],
    'expose-constants' => [],
];
