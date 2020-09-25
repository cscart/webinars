<?php

use OfflineSales\Service;
use OfflineSales\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

$schema[Service::SEO_OBJECT_TYPE] = [
    'table'          => '?:offline_sale_descriptions',
    'condition'      => '',
    'description'    => 'title',
    'dispatch'       => 'offline_sales.view',
    'item'           => 'offline_sale_id',
    'name'           => 'offline_sale.offline_sale',
    'not_shared'     => true,
    'html_options'   => ['file'],
    'option'         => 'seo_other_type',
    'exist_function' => static function ($sale_id) {
        return (bool) ServiceProvider::getRepository()->findOne($sale_id, CART_LANGUAGE);
    },
];

return $schema;
