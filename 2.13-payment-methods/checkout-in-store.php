<?php

use Tygh\Http;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Проведение платежа:
 * 1. Выполнить запрос к API платежного шлюза
 * 2. Задать новый статус заказа
 * 3. Сохранить информацию о платеже
 */

/**
 * @var array $order_info     Данные заказа
 * @var array $processor_data Данные способа оплаты.
 *                            Настройки способа оплаты доступны через $processor_data['processor_params'].
 */

// URL API платежного шлюза
$payment_gateway_api_url = 'https://api.example.com';

// Карта для преобразования статуса транзакции из API в статус заказа
$transaction_status_map = [
    'SUCCESS' => 'P', // paid
    'FAIL'    => 'F', // failed
];

// Данные для создания транзакции
$payment_request_data = [
    'PAYMENT_SUM'  => $order_info['total'],
    'REFERENCE_ID' => $order_info['order_id'],
    'CLIENT_EMAIL' => $order_info['user_data']['email'],
];

// Запрос к API платежного шлюза
$response = Http::post($payment_gateway_api_url, $payment_request_data);
// Ответ от API
$transaction = json_decode($response, true);
$transaction_status = $transaction['internal_status'];

/**
 * Переменная, которая содержит результат проведения платежа — новый статус заказа и информацию о платеже
 * Должна быть названа именно так!
 */
$pp_response = [
    // Новый статус заказа зависит от статуса транзакции
    'order_status'             => $transaction_status_map[$transaction_status] ?? 'F',
    // Причина, по которой для заказа выставлен текущий статус
    'reason_text'              => $transaction['transaction_description'],
    // Номер транзакции на платежном шлюзе
    'txn_id'                   => $transaction['transaction_id'],
    // Любая дополнительная информация, полученная из платежного уведомления
    'my_addon.internal_status' => $transaction['internal_status'],
    'my_addon.fraud_status'    => $transaction['fraud_status'],
];
