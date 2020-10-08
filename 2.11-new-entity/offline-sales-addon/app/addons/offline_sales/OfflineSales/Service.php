<?php

namespace OfflineSales;

use Tygh\Enum\ObjectStatuses;
use Tygh\Tools\SecurityHelper;

/**
 * Сервис для работы с распродажами.
 *
 * @package OfflineSales
 */
class Service
{
    /**
     * @var \OfflineSales\Repository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $lang_code;

    /**
     * @var int
     */
    protected $default_items_per_page;

    /**
     * @var \Tygh\Tools\SecurityHelper
     */
    protected $security_helper;

    /**
     * @var bool
     */
    protected $is_seo_enabled;

    /**
     * Идентификатор типа объекта для модуля SEO
     */
    const SEO_OBJECT_TYPE = 'o';

    /**
     * Конструктор сервиса.
     *
     * @param \OfflineSales\Repository   $repository             Репозиторий распродаж
     * @param \Tygh\Tools\SecurityHelper $security_helper        Сервис для санитизации данных
     * @param bool                       $is_seo_enabled         Включен ли модуль SEO
     * @param int                        $default_items_per_page Количество распродаж для поиска по умолчанию
     * @param string                     $lang_code              Код языка для поиска по умолчанию
     */
    public function __construct(
        Repository $repository,
        SecurityHelper $security_helper,
        $is_seo_enabled,
        $default_items_per_page,
        $lang_code
    ) {
        $this->repository = $repository;
        $this->security_helper = $security_helper;
        $this->is_seo_enabled = $is_seo_enabled;
        $this->default_items_per_page = $default_items_per_page;
        $this->lang_code = $lang_code;
    }

    /**
     * Ищет распродажи.
     *
     * @param array    $params         Параметры поиска
     * @param int|null $items_per_page Количество распродаж на страницу
     *
     * @return array[] Список распродаж и параметры поиска
     */
    public function findSales(array $params, $items_per_page = null)
    {
        if ($items_per_page === null) {
            $items_per_page = $this->default_items_per_page;
        }

        return $this->repository->find(
            $params,
            $items_per_page,
            $this->lang_code
        );
    }

    /**
     * Ищет только включенные распродажи.
     *
     * @param array    $params         Параметры поиска
     * @param int|null $items_per_page Количество распродаж на страницу
     *
     * @return array[] Список распродаж и параметры поиска
     */
    public function findActiveSales(array $params = [], $items_per_page = null)
    {
        if ($items_per_page === null) {
            $items_per_page = $this->default_items_per_page;
        }

        $params['status'] = ObjectStatuses::ACTIVE;
        list($sales, $search) = $this->repository->find(
            $params,
            $items_per_page,
            $this->lang_code
        );

        $sales = static::preloadAdditionalSalesData($sales, true, true, false);

        return [$sales, $search];
    }

    /**
     * Получает информацию о распродаже.
     *
     * @param int  $sale_id      Идентификатор распродажи
     * @param bool $get_image    Получать ли изображение распродажи
     * @param bool $get_store    Получать ли информацию о магазине
     * @param bool $get_products Получать ли информацию о товарах
     *
     * @return array|null Данные распродажи или null, если ничего не найдено
     */
    public function getSale($sale_id, $get_image = true, $get_store = false, $get_products = false)
    {
        $sale = $this->repository->findOne($sale_id, $this->lang_code);
        if (!$sale) {
            return null;
        }

        list($sale) = static::preloadAdditionalSalesData([$sale], $get_image, $get_store, $get_products);

        return $sale;
    }

    /**
     * Обновляет распродажу.
     *
     * @param int   $sale_id Идентификатор распродажи
     * @param array $data    Данные распродажи
     *
     * @return int Идентификатор распродажи
     */
    public function updateSale($sale_id, array $data)
    {
        $data['offline_sale_id'] = $sale_id;

        // Прочистить данные распродажи
        $this->security_helper->sanitizeObjectData('offline_sale', $data);

        // Обновить данные распродажи
        $sale_id = $this->repository->update($data, $this->lang_code);

        // Обновить SEO-имя
        if ($this->is_seo_enabled) {
            fn_seo_update_object($data, $sale_id, self::SEO_OBJECT_TYPE, $this->lang_code);
        }

        // Обновить картинки
        fn_attach_image_pairs('offline_sale', 'offline_sale', $sale_id, $this->lang_code);

        return $sale_id;
    }

    /**
     * Удаляет распродажу.
     *
     * @param int $sale_id Идентификатор распродажи
     */
    public function deleteSale($sale_id)
    {
        if (!$this->getSale($sale_id, false)) {
            return;
        }

        // Удалить данные распродажи
        $this->repository->delete($sale_id);

        // Удалить SEO-имя
        if ($this->is_seo_enabled) {
            fn_delete_seo_name($sale_id, self::SEO_OBJECT_TYPE);
        }

        // Удалить картинки
        fn_delete_image_pairs($sale_id, 'offline_sale');
    }

    /**
     * Подгружает дополнительные данные для распродаж.
     *
     * @param array $sales        Список распродаж
     * @param bool  $get_image    Получать ли изображение распродажи
     * @param bool  $get_store    Получать ли информацию о магазине
     * @param bool  $get_products Получать ли информацию о товарах
     *
     * @return array
     */
    protected function preloadAdditionalSalesData(
        array $sales,
        $get_image = true,
        $get_store = false,
        $get_products = false
    ) {
        $images = [];
        if ($get_image) {
            $images = fn_get_image_pairs(
                array_column($sales, 'offline_sale_id'),
                'offline_sale',
                'M',
                true,
                true
            );
        }

        return array_map(
            function ($sale) use ($images, $get_store, $get_products) {
                // Загрузить полную информацию о магазине
                if ($get_store) {
                    $sale['store'] = fn_get_store_location($sale['store_id'], $this->lang_code);
                }

                // Загрузить товары
                if ($get_products) {
                    if (empty($sale['product_ids'])) {
                        $sale['products'] = [];
                    } else {
                        list($sale['products'],) = fn_get_products(['pid' => $sale['product_ids']]);
                        fn_gather_additional_products_data($sale['products'], ['get_icon' => true, 'get_detailed' => true]);
                    }
                }

                // Загрузить SEO-имя
                if ($this->is_seo_enabled && empty($sale['seo_name'])) {
                    $sale['seo_name'] = fn_seo_get_name(
                        self::SEO_OBJECT_TYPE,
                        $sale['offline_sale_id'],
                        '',
                        null,
                        $this->lang_code
                    );
                }

                if (!isset($images[$sale['offline_sale_id']])) {
                    return $sale;
                }

                // Заполнить изоброажения
                $sale['main_pair'] = reset($images[$sale['offline_sale_id']]);

                return $sale;
            },
            $sales
        );
    }
}
