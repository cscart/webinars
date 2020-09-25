<?php

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */

// Добавляет пункт в центральное меню Marketing
$schema['central']['marketing']['items']['offline_sales.offline_sales'] = [
    'attrs' => [
        'class' => 'is-addon',
    ],
    'href'  => 'offline_sales.manage',
    'alt'   => 'offline_sales.update,offline_sales.manage',
];

return $schema;
