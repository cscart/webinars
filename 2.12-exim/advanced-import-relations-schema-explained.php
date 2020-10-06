<?php

use Tygh\Addons\AdvancedImport\Presets\Manager;

return [
    // 'products' указывать обязательно — это показывает, что отношения связаны именно с импортом товаров
    'products' => [
        // Идентификатор отношения
        'related_entity' => [
            // Идентификатор языковой переменной для отображения группы отношения в селекторе сопоставляемых полей
            'description'        => 'my_addon.related_entities',
            // Продайдер значений для селектора сопоставляемых полей
            'items_function'     => static function (Manager $preset_manager, array $relation) {
                // Ключи массива — идентификаторы объектов
                return [
                    1 => [
                        // Название объекта
                        'description'      => 'Related Entity #1',
                        // Показывать идентификатор объекта
                        'show_name'        => false,
                        // Показывать название объекта
                        'show_description' => true,
                    ],
                    2 => [
                        // Название объекта
                        'description'      => 'Related Entity #2',
                        // Показывать идентификатор объекта
                        'show_name'        => false,
                        // Показывать название объекта
                        'show_description' => true,
                    ],
                ];
            },
            // Обработчик, преобразующий агрегированное сопоставление в значение свойства товара
            'aggregate_function' => static function (array $product_row, array $aggregated_property) {
                $feature_values = [];
                // $id — имеет вид {идентификатор отношения}_{идентификатор объекта}
                // $value — значение соответствующего поля товара из файла импорта
                foreach ($aggregated_property['values'] as $id => $value) {
                    list(, $key) = explode('_', $id);
                    $feature_values[$key] = $value;
                }

                return $feature_values;
            },
            // Свойство товара, которое будет агрегировать сопоставление со связанными сущностями
            'aggregate_field'    => 'Advanced Import: Features',
        ],
    ],
];
