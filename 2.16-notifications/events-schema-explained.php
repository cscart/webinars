<?php

use Tygh\Enum\UserTypes;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Notifications\Transports\Mail\MailTransport;

return [
    // Идентификатор события
    'my_addon.my_entity.status_changed.open' => [
        // Группа, к которой относится событие
        'group'         => 'my_addon.entities',
        'name'          => [
            // Языковая переменная, которая будет использована в качестве шаблона для названия события
            'template' => 'my_addon.my_entity.status_changed.name',
            // Подстановочные переменные для замены в шаблоне названия события
            'params'   => [
                // Параметр для замены => Значение
                '[status]' => 'Open',
            ],
        ],
        // Провайдер данных события — подгружает информацию, не переданную в контексте
        'data_provider' => static function (array $data) {
            $data['entity_data'] = fn_my_addon_get_entity_data($data['entity_id']);

            return $data;
        },
        //  Получатели уведомления о событии
        'receivers'     => [
            // Тип получателя
            UserTypes::CUSTOMER => [
                // Класс транспорта, который отправляет уведомление => правила формирования сообщения из данных события
                MailTransport::getId() => MailMessageSchema::create(
                    [
                        // Напрямую заданные значения
                        'directly_specified_value_1' => AREA,
                        'directly_specified_value_2' => 42,
                        'directly_specified_value_3' => false,
                        // Извлечение данных из контекста события
                        'value_from_event_context_1' => DataValue::create('parent_key.key_1'),
                        // Извлечение данных из контекста события с фоллбеком на значение по умолчанию
                        'value_from_event_context_2' => DataValue::create('parent_key.key_2', 'defaultValue'),
                        // Обработчик, который модифицирует подготовленные данные сообщения
                        'data_modifier'              => static function (array $data) {
                            $data['directly_specified_value_1'] = (string) $data['directly_specified_value_1'];
                            $data['value_from_event_context_1'] = (bool) $data['value_from_event_context_1'];

                            return $data;
                        },
                    ]
                ),
            ],
        ],
    ],
];
