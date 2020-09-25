<?php

use OfflineSales\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

// Регистрирует провайдер сервисов в приложении
Tygh::$app->register(new ServiceProvider());
