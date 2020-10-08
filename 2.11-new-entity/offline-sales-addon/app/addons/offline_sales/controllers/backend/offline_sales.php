<?php

use OfflineSales\ServiceProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

// Возвращает HTML в параметр запроса offline_sale_data
fn_trusted_vars(
    'offline_sale_data'
);

// Любые деструктивные действия — только POST'ом
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update') {
        $sale_id = ServiceProvider::getService()->updateSale(
            $_REQUEST['offline_sale_id'],
            $_REQUEST['offline_sale_data']
        );

        return [CONTROLLER_STATUS_OK, 'offline_sales.update?offline_sale_id=' . $sale_id];
    }

    if ($mode === 'delete') {
        ServiceProvider::getService()->deleteSale($_REQUEST['offline_sale_id']);

        return [CONTROLLER_STATUS_OK, 'offline_sales.manage'];
    }
}

if ($mode === 'manage' || $mode === 'update' || $mode === 'add') {
    // Добавляет меню внутренней навигации на страницы управления распродажами
    Registry::set('navigation.dynamic.sections', [
        'offline_sales' => [
            'href'  => 'offline_sales.manage',
            'title' => __('offline_sales.offline_sales'),
        ],
        'stores'        => [
            'href'  => 'store_locator.manage',
            'title' => __('store_locator'),
        ],
    ]);

    // Помечает активный пункт меню
    Registry::set('navigation.dynamic.active_section', 'offline_sales');
}

// Режим для просмотра списка распродаж
if ($mode === 'manage') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    list($offline_sales, $search) = ServiceProvider::getService()->findSales($_REQUEST);

    $view->assign('offline_sales', $offline_sales);
    $view->assign('search', $search);

    return [CONTROLLER_STATUS_OK];
}

// Режим для создания/редактирования распродажи
if ($mode === 'update' || $mode === 'add') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    $sale_id = null;
    $sale = null;
    if ($mode === 'update') {
        if (empty($_REQUEST['offline_sale_id'])) {
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        $sale_id = $_REQUEST['offline_sale_id'];
        $sale = ServiceProvider::getService()->getSale($sale_id);
        if (!$sale) {
            return [CONTROLLER_STATUS_NO_PAGE];
        }
    }

    $view->assign('offline_sale_id', $sale_id);
    $view->assign('offline_sale_data', $sale);

    // Задает вкладки на странице редактирования распродажи
    Registry::set('navigation.tabs', [
        'general'  => [
            'title' => __('general'),
            'js'    => true,
        ],
        'products' => [
            'title' => __('offline_sales.discounted_products'),
            'js'    => true,
        ],
    ]);

    return [CONTROLLER_STATUS_OK];
}

// Режим для подгрузки списка магазинов с поиском
if ($mode === 'get_stores') {
    if (!defined('AJAX_REQUEST')) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    list($stores, $search) = fn_get_store_locations($_REQUEST, $_REQUEST['page_size']);

    /** @var \Tygh\Ajax $ajax */
    $ajax = Tygh::$app['ajax'];

    $objects = array_map(
        static function ($data) {
            return [
                'id'   => $data['store_location_id'],
                'text' => $data['name'],
            ];
        },
        array_values($stores)
    );

    $ajax->assign('objects', $objects);
    $ajax->assign('total_objects', $search['total_items']);

    return [CONTROLLER_STATUS_NO_CONTENT];
}
