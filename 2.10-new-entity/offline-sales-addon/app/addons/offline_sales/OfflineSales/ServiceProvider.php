<?php

namespace OfflineSales;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;
use Tygh\Tools\SecurityHelper;
use Tygh\Tygh;

/**
 * Предоставляет сервисы модуля.
 *
 * @package OfflineSales
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Регистрирует провайдеры сервисов в приложении.
     *
     * @param \Pimple\Container $app Приложение
     */
    public function register(Container $app)
    {
        // Регистрирует провайдер сервиса: Сервис распродаж.
        $app['offline_sales.service'] = static function (Container $app) {
            return new Service(
                static::getRepository(),
                new SecurityHelper(),
                Registry::get('addons.seo.status') === ObjectStatuses::ACTIVE,
                (int) Registry::ifGet('settings.Appearance.admin_elements_per_page', 10),
                DESCR_SL
            );
        };

        // Регистрирует провайдер сервиса: Репозиторий распродаж.
        $app['offline_sales.repository'] = static function (Container $app) {
            return new Repository($app['db']);
        };
    }

    /**
     * Предоставляет сервис для работы с распродажами.
     *
     * @return \OfflineSales\Service
     */
    public static function getService()
    {
        return Tygh::$app['offline_sales.service'];
    }

    /**
     * Предоставляет репозиторий распродаж.
     *
     * @return \OfflineSales\Repository
     */
    public static function getRepository()
    {
        return Tygh::$app['offline_sales.repository'];
    }
}
