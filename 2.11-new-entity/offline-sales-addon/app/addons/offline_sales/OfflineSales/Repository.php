<?php

namespace OfflineSales;

use Tygh\Database\Connection;

/**
 * Репозиторий для работы с распродажами.
 *
 * @package OfflineSales
 */
class Repository
{
    /**
     * @var \Tygh\Database\Connection
     */
    protected $db;

    /**
     * Конструктор класса.
     *
     * @param \Tygh\Database\Connection $db Соединение с БД для выполнения запросов
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Ищет распродажи.
     *
     * @param array  $params         Параметры поиска
     * @param int    $items_per_page Количество распродаж на страницу
     * @param string $lang_code      Код языка для описаний
     *
     * @return array[] Список распродаж, параметры поиска
     */
    public function find(array $params, $items_per_page, $lang_code)
    {
        // Заполняет не переданные параметры стандартными значениями
        $params = array_merge(
            [
                'offline_sale_id' => null,
                'store_id'        => null,
                'status'          => null,
                'items_per_page'  => $items_per_page,
                'page'            => 1,
            ],
            $params
        );

        // Поля для выборки из БД
        $fields = [
            'sale_id'     => 'offline_sales.offline_sale_id',
            'store_id'    => 'offline_sales.store_id',
            'product_ids' => 'offline_sales.product_ids',
            'status'      => 'offline_sales.status',
            'title'       => 'descriptions.title',
            'description' => 'descriptions.description',
        ];

        // JOIN'ы
        $joins = [
            'descriptions' => $this->db->quote(
                'INNER JOIN ?:offline_sale_descriptions AS descriptions' .
                ' ON descriptions.offline_sale_id = offline_sales.offline_sale_id' .
                ' AND descriptions.lang_code = ?s',
                $lang_code
            ),
        ];

        // Условия поиска
        $conditions = [];

        if ($params['offline_sale_id']) {
            $conditions['offline_sale_id'] = $this->db->quote(
                'AND offline_sales.offline_sale_id IN (?n)',
                (array) $params['offline_sale_id']
            );
        }

        if ($params['status']) {
            $conditions['status'] = $this->db->quote(
                'AND offline_sales.status IN (?a)',
                (array) $params['status']
            );
        }

        // Паджинация
        $limit = '';
        if ($params['items_per_page']) {
            $limit = db_paginate($params['page'], $params['items_per_page']);
        }

        // Получает список распродаж
        $sales = $this->db->getArray(
            'SELECT ?p' .
            ' FROM ?:offline_sales AS offline_sales' .
            ' ?p' .
            ' WHERE 1=1 ?p' .
            ' GROUP BY offline_sales.offline_sale_id' .
            ' ?p',
            implode(',', $fields),
            implode(' ', $joins),
            implode(' ', $conditions),
            $limit
        );

        // Получает общее количество распродаж (для паджинации)
        if ($params['items_per_page']) {
            $params['total_items'] = $this->db->getField(
                'SELECT COUNT(offline_sales.offline_sale_id)' .
                ' FROM ?:offline_sales AS offline_sales' .
                ' ?p' .
                ' WHERE 1=1 ?p',
                implode(' ', $joins),
                implode(' ', $conditions)
            );
        }

        return [$sales, $params];
    }

    /**
     * Возвращает одну распродажу.
     *
     * @param int    $sale_id   Идентификатор распродажи
     * @param string $lang_code Код языка для описаний
     *
     * @return array|null Данные распродажи или null, если ничего не найдено
     */
    public function findOne($sale_id, $lang_code)
    {
        list($sales) = $this->find(['offline_sale_id' => $sale_id], 1, $lang_code);
        if (!$sales) {
            return null;
        }

        return reset($sales);
    }

    /**
     * Создает распродажу.
     *
     * @param array $data Данные распродажи
     *
     * @return int Идентификатор распродажи
     */
    public function create(array $data)
    {
        $sale_id = (int) $this->db->query('INSERT INTO ?:offline_sales ?e', $data);

        fn_create_description('offline_sale_descriptions', 'offline_sale_id', $sale_id, $data);

        return $sale_id;
    }

    /**
     * Обновляет распродажу.
     *
     * @param array  $data      Данные распродажи
     * @param string $lang_code Код языка для описаний
     *
     * @return int Идентификатор распродажи
     */
    public function update(array $data, $lang_code)
    {
        if (empty($data['offline_sale_id'])) {
            return $this->create($data);
        }

        $this->db->query(
            'UPDATE ?:offline_sales SET ?u' .
            ' WHERE offline_sale_id = ?i',
            $data,
            $data['offline_sale_id']
        );

        $this->db->query(
            'UPDATE ?:offline_sale_descriptions SET ?u' .
            ' WHERE offline_sale_id = ?i' .
            ' AND lang_code = ?s',
            $data,
            $data['offline_sale_id'],
            $lang_code
        );

        return $data['offline_sale_id'];
    }

    /**
     * Удаляет распродажу.
     *
     * @param int $sale_id Идентификатор распродажи
     */
    public function delete($sale_id)
    {
        $this->db->query(
            'DELETE FROM ?:offline_sales' .
            ' WHERE offline_sale_id = ?i',
            $sale_id
        );
        $this->db->query(
            'DELETE FROM ?:offline_sale_descriptions' .
            ' WHERE offline_sale_id = ?i',
            $sale_id
        );
    }
}
