<?php

/**
 * значения можно использовать как {$var} - заменится на соответствующую директиву из конфига
 * так можно и использовать {%var%} заменится на директиву из класса (Settings)
 * ВАЖНО! директивы вида {%var%} нужно передавать через методы конфигураторы.
 * Например, если передать параметр через конструктор (в блоке arguments), то такие параметры не будут заменены
 * на settings. Такие директивы нужно передавать через дополнительный метод, указанный в блоке calls
 * 
 *  Money::class => [
        'class' => Money::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
        'calls' => [
            [
                'method' => 'configure',
                'arguments' => [
                    new PR('money.thousands_separator'),
                ]
            ],
        ]
    ],
 */

return [
    'root_dir' => '{$root_dir}',
    'logger' => [
        'file' => __DIR__ . '/../../log/app.log',
        'max_files_rotation' => 10,
    ],
    'db' => [
        'driver'   => '{$db_driver}',
        'dsn'      => '{$db_driver}:host={$db_server};dbname={$db_name};charset={$db_charset}',
        'user'     => '{$db_user}',
        'password' => '{$db_password}',
        'prefix'   => '{$db_prefix}',
        'db_sql_mode' => '{$db_sql_mode}',
        'db_timezone' => '{$db_timezone}',
        'db_names' => '{$db_names}',
    ],
    'config' => [
        'config_file' => __DIR__ . '/../../../config/config.php',
        'config_local_file' => __DIR__ . '/../../../config/config.local.php',
        'version' => '{$version}',
    ],
    'manager_menu' => [
        'dev_mode' => '{$dev_mode}',
    ],
    'template_config' => [
        'scripts_defer' => '{$scripts_defer}',
        'them_settings_filename' => 'theme-settings.css',
        'compile_css_dir' => 'cache/css/',
        'compile_js_dir' => 'cache/js/',
    ],
    
    /**
     * Настройки адапреров системы. Адапрер это по сути класс, который лежит в Okay\Core\Adapters\XXX
     * Где XXX уже подвид адапреров
     */
    'adapters' => [
        'resize' => [
            'default_adapter' => '{$resize_adapter}',
            'watermark' => '{$watermark_file}',
            'watermark_offset_x' => '{%watermark_offset_x%}',
            'watermark_offset_y' => '{%watermark_offset_y%}',
            'image_quality' => '{%image_quality%}',
        ],
        'response' => [
            'default_adapter' => 'Html',
        ],
    ],
    'money' => [
        'decimals_point' => '{%decimals_point%}',
        'thousands_separator' => '{%thousands_separator%}',
    ],
    'plugins' => [
        'date' => [
            'date_format' => '{%date_format%}',
        ],
    ],
    'design' => [
        'smarty_caching'        => '{$smarty_caching}',
        'smarty_debugging'      => '{$smarty_debugging}',
        'smarty_html_minify'    => '{$smarty_html_minify}',
        'smarty_compile_check'  => '{$smarty_compile_check}',
        'smarty_security'       => '{$smarty_security}',
        'smarty_cache_lifetime' => '{$smarty_cache_lifetime}',
        'smarty_force_compile'  => '{$smarty_force_compile}',
        'debug_translation'     => '{$debug_translation}',
    ],
    'seo' => [
        'canonical' => [
            'catalog_pagination' => '{%canonical_catalog_pagination%}',
            'catalog_page_all' => '{%canonical_catalog_page_all%}',
            'category_brand' => '{%canonical_category_brand%}',
            'category_features' => '{%canonical_category_features%}',
            'catalog_other_filter' => '{%canonical_catalog_other_filter%}',
            'catalog_filter_pagination' => '{%canonical_catalog_filter_pagination%}',
        ],
        'robots' => [
            'catalog_pagination' => '{%robots_catalog_pagination%}',
            'catalog_page_all' => '{%robots_catalog_page_all%}',
            'category_brand' => '{%robots_category_brand%}',
            'category_features' => '{%robots_category_features%}',
            'catalog_other_filter' => '{%robots_catalog_other_filter%}',
            'catalog_filter_pagination' => '{%robots_catalog_filter_pagination%}',
        ],
        'filter' => [
            'max_brands_filter_depth' => '{%max_brands_filter_depth%}',
            'max_other_filter_depth' => '{%max_other_filter_depth%}',
            'max_features_filter_depth' => '{%max_features_filter_depth%}',
            'max_features_values_filter_depth' => '{%max_features_values_filter_depth%}',
            'max_filter_depth' => '{%max_filter_depth%}',
        ],
    ],
];
