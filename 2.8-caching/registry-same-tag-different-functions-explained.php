<?php

use Tygh\Registry;

/**
 * В папке var/cache/registry будет создана папка catalog, внутри которой будет лежать запись
 * - cache_customerCategories
 */
function fn_cache_categories()
{
    $cache_tag = 'catalog';
    $cache_key = 'Categories';
    $cache_tables = ['categories'];
    Registry::registerCache(
        [$cache_tag, $cache_key],
        $cache_tables,
        Registry::cacheLevel('static')
    );

    if (Registry::isExist($cache_key)) {
        return;
    }

    list($categories,) = fn_get_categories([]);
    Registry::set($cache_key, $categories);

    /**
     * $ ls var/cache/registry/catalog
     * cache_customerCategories
     */
}

/**
 * В папке var/cache/registry будет создана папка catalog, внутри которой будет лежать запись
 * - cache_customerProducts
 */
function fn_cache_products()
{
    $cache_tag = 'catalog';
    $cache_key = 'Products';
    $cache_tables = ['products'];
    Registry::registerCache(
        [$cache_tag, $cache_key],
        $cache_tables,
        Registry::cacheLevel('static')
    );

    if (Registry::isExist($cache_key)) {
        return;
    }

    list($products,) = fn_get_products([]);
    Registry::set($cache_key, $products);

    /**
     * $ ls var/cache/registry/catalog
     * cache_customerProducts
     */
}

/**
 * Обновление любой из двух таблиц — products или categories, — приведет к удалению обоих записей из кэша:
 * - var/cache/registry/catalog/cache_customerProducts
 * - var/cache/registry/catalog/cache_customerCategories
 */

function fn_bootstrap_cache_cleanup(bool $clean_categories_cache = false, bool $clean_products_cache = false)
{
    if ($clean_categories_cache) {
        db_query('UPDATE ?:categories SET status = ?s', 'D');
        db_query('UPDATE ?:categories SET status = ?s', 'A');
    }

    if ($clean_products_cache) {
        db_query('UPDATE ?:products SET status = ?s', 'D');
        db_query('UPDATE ?:products SET status = ?s', 'A');
    }

    /**
     * $ ls var/cache/registry/catalog
     * ls: var/cache/registry/catalog: No such file or directory
     */
}
