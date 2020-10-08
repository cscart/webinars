<?php

use OfflineSales\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Предоставляет список распродаж для вывода в блоке.
 *
 * @return array Список распродаж
 */
function fn_offline_sales_blocks_get_offline_sales($content, $block, $schema)
{
    list($sales,) = ServiceProvider::getService()->findActiveSales(
        [],
        $block['properties']['displayed_offline_sales']
    );

    return $sales;
}
