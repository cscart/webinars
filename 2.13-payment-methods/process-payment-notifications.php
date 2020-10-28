<?php

defined('BOOTSTRAP') or die('Access denied');

if (defined('PAYMENT_NOTIFICATION')) {
    /**
     * Обработка платежного уведомления:
     * 1. Задать новый статус заказа
     * 2. Сохранить информацию о платеже
     */

    /**
     * @var string $mode Режим запроса к контроллеру payment_notification
     */

    // Определить ID заказа
    $order_id = $_REQUEST['order_id'];

    // Проверить что заказ оплачивался именно этим способом оплаты
    if (!fn_check_payment_script('my_payment.php', $order_id)) {
        die('Access denied');
    }

    // Обработка запроса к payment_notification.notify
    if ($mode === 'notify') {
        // Карта для преобразования статуса транзакции в статус заказа
        $transaction_status_map = [
            'paid'     => 'P', // paid
            'canceled' => STATUS_CANCELED_ORDER,
            'refunded' => 'R', // refunded
            'fraud'    => 'F', // failed
        ];

        // Статус транзакции на стороне платежного шлюза
        $transaction_status = $_REQUEST['internal_status'];

        $pp_response = [
            // Новый статус заказа зависит от запрошенного режима работы контроллера
            'order_status' => $transaction_status_map[$transaction_status] ?? 'F',
            // Причина, по которой для заказа выставлен текущий статус
            'reason_text'  => $_REQUEST['transaction_description'],
        ];

        // Сменить статуса заказа и сохранить информацию о платеже
        fn_update_order_payment_info($order_id, $pp_response);

        echo 'OK';
        exit(0);
    }

    // Карта для преобразования режима запроса в статус заказа
    $mode_to_status_map = [
        'return' => 'O', // open
        'cancel' => STATUS_CANCELED_ORDER,
    ];

    $pp_response = [
        // Новый статус заказа зависит от запрошенного режима работы контроллера
        'order_status'             => $mode_to_status_map[$mode] ?? 'F',
        // Причина, по которой для заказа выставлен текущий статус
        'reason_text'              => $_REQUEST['transaction_description'],
        // Номер транзакции на платежном шлюзе
        'txn_id'                   => $_REQUEST['transaction_id'],
        // Любая дополнительная информация, полученная из платежного уведомления
        'my_addon.internal_status' => $_REQUEST['internal_status'],
        'my_addon.fraud_status'    => $_REQUEST['fraud_status'],
    ];

    // Сменить статуса заказа и сохранить информацию о платеже
    fn_finish_payment($order_id, $pp_response);

    // Перенаправить покупателя на целевую страницу заказа или страницу оформления заказа при ошибке
    fn_order_placement_routines('route', $order_id);
}

/**
 * Проведение платежа:
 * 1. Подготовить данные для отправки на платежный шлюз
 * 2. Перенаправить покупателя на платежный шлюз
 */

/**
 * @var int   $order_id       Номер заказа
 * @var array $order_info     Данные заказа
 * @var array $processor_data Данные способа оплаты.
 *                            Настройки способа оплаты доступны через $processor_data['processor_params']
 */

// URL платежного шлюза для перенаправления пользователя
$payment_gateway_url = 'https://pay.example.com';

// Данные, которые нужно передать на платежный шлюз
$payment_request_data = [
    'PAYMENT_SUM'  => $order_info['total'],
    'REFERENCE_ID' => $order_info['order_id'],
    'CLIENT_EMAIL' => $order_info['user_data']['email'],
    // URL для возврата клиента в магазин после оплаты — формируется через контроллер payment_notification
    'RETURN_URL'   => fn_url('payment_notification.return?payment=my_payment&order_id=' . $order_id),
    // URL для возврата клиента в магазин при отмене платежа — формируется через контроллер payment_notification
    'CANCEL_URL'   => fn_url('payment_notification.cancel?payment=my_payment&order_id=' . $order_id),
    // URL для отправки платежных уведомлений при изменении статуса транзакции на платежном шлюзе
    'NOTIFY_URL'   => fn_url('payment_notification.notify?payment=my_payment&order_id=' . $order_id),
];

// Перенаправить покупателя на платежный шлюз
fn_create_payment_form(
    $payment_gateway_url,
    $payment_request_data,
    __('my_addon.my_payment')
);
