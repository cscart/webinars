<?php

use Tygh\Tools\SecurityHelper;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */

// Для объектов offline_sale из title будет удаляться HTML
$schema['offline_sale'] = [
    SecurityHelper::SCHEMA_SECTION_FIELD_RULES => [
        'title' => SecurityHelper::ACTION_REMOVE_HTML,
    ],
];

return $schema;
