<?php

use OfflineSales\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

/** @var \Tygh\SmartyEngine\Core $view */
$view = Tygh::$app['view'];

// Режим для просмотра списка распродаж
if ($mode === 'search') {
    list($offline_sales, $search) = ServiceProvider::getService()->findActiveSales($_REQUEST);

    $view->assign('offline_sales', $offline_sales);
    $view->assign('search', $search);

    // Добавляет хлебную крошку с разделом распродаж
    fn_add_breadcrumb(__('offline_sales.offline_sales'));

    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'view') {
    $sale_id = null;
    if (isset($_REQUEST['offline_sale_id'])) {
        $sale_id = (int) $_REQUEST['offline_sale_id'];
    }
    if (!$sale_id) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $sale = ServiceProvider::getService()->getSale($sale_id, true, true, true);
    if (!$sale) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $view->assign('offline_sale_data', $sale);

    // Добавляет хлебную крошку с разделом распродаж
    fn_add_breadcrumb(__('offline_sales.offline_sales'), 'offline_sales.search');

    // Добавляет хлебную крошку с текущей распродажей
    fn_add_breadcrumb($sale['title']);

    return [CONTROLLER_STATUS_OK];
}
