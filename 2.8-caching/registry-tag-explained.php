<?php

use Tygh\Registry;

/**
 * В папке var/cache/registry будут создаваться папки вида
 * - categories_data_1
 * - categories_data_2
 * - ...
 * - categories_data_N
 *
 * В каждой такой папке будет храниться одна запись.
 *
 * @param int $company_id
 *
 * @return array
 */
function fn_get_categories_with_cache(int $company_id): array
{
    $cache_key = "categories_data_{$company_id}";
    $cache_tables = ['categories'];
    Registry::registerCache(
        $cache_key,
        $cache_tables,
        Registry::cacheLevel('static')
    );

    if (Registry::isExist($cache_key)) {
        return Registry::get($cache_key);
    }

    list($categories,) = fn_get_categories(['company_id' => $company_id]);
    Registry::set($cache_key, $categories);

    return $categories;
}

/**
 * В папке var/cache/registry будет создана папка categories_data,
 * внутри которой будут лежать записи
 * - cache_customerCategories_data_1
 * - cache_customerCategories_data_2
 * - ...
 * - cache_customerCategories_data_N
 *
 * @param int $company_id
 *
 * @return array
 */
function fn_get_categories_with_tagged_cache(int $company_id): array
{
    $cache_tag = 'categories_data';
    $cache_key = "Categories_data_{$company_id}";
    $cache_tables = ['categories'];
    Registry::registerCache(
        [$cache_tag, $cache_key],
        $cache_tables,
        Registry::cacheLevel('static')
    );

    if (Registry::isExist($cache_key)) {
        return Registry::get($cache_key);
    }

    list($categories,) = fn_get_categories(['company_id' => $company_id]);
    Registry::set($cache_key, $categories);

    return $categories;
}
