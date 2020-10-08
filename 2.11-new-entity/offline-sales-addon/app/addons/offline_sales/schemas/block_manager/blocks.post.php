<?php

defined('BOOTSTRAP') or die('Access denied');

require_once __DIR__ . '/blocks.functions.php';

/** @var array $schema */

$schema['offline_sales'] = [
    'templates' => 'addons/offline_sales/blocks',
    'content'   => [
        'offline_sales' => [
            'type'     => 'function',
            /** @see \fn_offline_sales_blocks_get_offline_sales() */
            'function' => ['fn_offline_sales_blocks_get_offline_sales'],
        ],
    ],
    'settings'  => [
        'displayed_offline_sales' => [
            'option_name'   => 'offline_sales.displayed_offline_sales',
            'type'          => 'input',
            'default_value' => 4,
        ],
    ],
    'wrappers'  => 'blocks/wrappers',
    'cache'     => [
        'update_handlers' => [
            'offline_sales',
            'offline_sale_descriptions',
        ],
    ],
];

return $schema;
