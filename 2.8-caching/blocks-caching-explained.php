<?php

return [
    // ...
    'cache' => [
        // Параметры запроса, значения которых будут использоваться для формирования ключа кэша
        'request_handlers'            => [
            'request_param_1',
        ],
        // Параметры сессии, значения которых будут использоваться для формирования ключа кэша
        'session_handlers'            => [
            'cart.param_1',
            'cart.user_data.param_2',
        ],
        // Названия куков, значения которых будут использоваться для формирования ключа кэша
        'cookie_handlers'             => [
            '%ALL%', // "магическое" значение — учитывать все куки
        ],
        // Параметры аутентификации (Tygh::$app['session']['auth']), значения которых будут использоваться для формирования ключа кэша
        'auth_handlers'               => [
            'user_id',
        ],
        // Таблицы, изменения в которых инвалидируют кэш для этого типа блока
        'update_handlers'             => [
            'table_1',
            'table_2',
        ],
        // Коллбеки, результаты которых будут использоваться для формирования ключа кэша
        'callable_handlers'           => [
            'callable_condition_1' => ['fn_get_callable_condition_value'],
        ],
        // Условия отключения кэша блока
        'disable_cache_when'          => [
            // Условия для параметров запроса, при соблюдении которых кэширование не произойдет
            'request_handlers'  => [
                'request_param_1' => ['gt', 0],
                // Допустимые операторы:
                // gt (>), lt (<), gte (>=), lte (<=), eq (==), neq (!=),
                // in (входит в массив значений), nin (не входит в массив значений),
                // cont (содержит подстроку), ncont (не содержит подстроку)
            ],
            // Условия для параметров сессии, при соблюдении которых кэширование не произойдет
            'session_handlers'  => [
                // Аналогично request_handlers
            ],
            // Условия для куков, при соблюдении которых кэширование не произойдет
            'cookie_handlers'   => [
                // Аналогично request_handlers
            ],
            // Условия для параметров аутентификации (Tygh::$app['session']['auth']), при соблюдении которых кэширование не произойдет
            'auth_handlers'     => [
                // Аналогично request_handlers
            ],
            // Коллбеки, при возврате true из которых кэширование не произойдет
            'callable_handlers' => [
                'callable_disable_condition_1' => ['fn_is_cache_disabled_for_callable_condition'],
            ],
        ],
        // Условия для принудительного сброса кэша
        'regenerate_cache_when' => [
            // Аналогично disable_cache_when
        ],
        // Перегрузка правил кэширования блока для отдельных маршрутов
        'cache_overrides_by_dispatch' => [
            // Структура схемы для маршрута такая же, как и для блока в целом
            'controller.mode' => [
                'request_handlers'   => [],
                'session_handlers'   => [],
                'cookie_handlers'    => [],
                'auth_handlers'      => [],
                'update_handlers'    => [],
                'callable_handlers'  => [],
                'disable_cache_when' => [],
            ],
        ],
    ],
    // ...
];
