<?php

use Tygh\Registry;

function fn_get_products_with_lock()
{
    $cache_key = 'products';

    // Пометим, что запись нужно кэшировать
    Registry::registerCache($cache_key, ['products'], Registry::cacheLevel('static'));

    // Проверим, есть ли в кэше нужное значение
    $is_result_cached = Registry::isExist($cache_key);

    $lock = null;
    if (!$is_result_cached) {
        /** @var \Tygh\Lock\Factory $lock_factory */
        $lock_factory = Tygh::$app['lock.factory'];

        // Если результата в кэше нет — заблокируем запись кэша
        $lock = $lock_factory->createLock($cache_key);

        // Если блокировка выставлена ранее — ждем ее снятия и проверяем, что там в кэше
        if (!$lock->acquire() && $lock->wait()) {
            $is_result_cached = Registry::loadFromCache($cache_key);
        }
    }

    if ($is_result_cached) {
        // Если в кэше есть данные — берем их
        $products = Registry::get($cache_key);
    } else {
        // Если в кэше пусто — грузим данные из БД
        list($products,) = fn_get_products([]);
        Registry::set($cache_key, $products);

        // Если была блокировка — снимаем ее
        if ($lock !== null) {
            $lock->release();
        }
    }

    return $products;
}
