<?php

return [
    // Название контроллера
    'controller_demo' => [
        // Будет использовано, если не найдутся конкретные условия для режима
        'permissions' => false,
        'modes'       => [
            // Название режима
            'mode_boolean'        => [
                // Абсолютное правило
                'permissions' => true,
            ],
            'mode_privilege'      => [
                // Правило, использующее привилегию
                'permissions' => 'some_privilege_name',
            ],
            'mode_request_method' => [
                // Правило, использующее метод запроса
                'permissions' => ['GET' => true, 'POST' => false],
            ],
            'mode_request_param'  => [
                // Правило, использующее параметр запроса
                'param_permissions' => [
                    'some_request_parameter_name' => [
                        'some_forbidden_value' => false,
                        'some_allowed_value'   => true,
                    ],
                ],
            ],
            'mode_conditions'     => [
                'permissions' => false,
                // Правило, использующее условие с функцией
                'condition'   => [
                    'operator' => 'or',
                    'function' => ['fn_foo'],
                ],
                // Условия могут быть вложенными
                'condition'   => [
                    'user_type' => [
                        \Tygh\Enum\UserTypes::ADMIN => [
                            'operator' => 'and',
                            'function' => ['fn_bar'],
                        ],
                    ],
                ],
            ],
            'mode_companies'      => [
                // Правило, использующее ограничение по вендору
                'vendor_only' => true,
                // Форсировать company_id при выполнении запроса
                'use_company' => true,
            ],
        ],
    ],
];
