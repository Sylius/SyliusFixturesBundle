<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Monolog\Level;
use Monolog\Logger;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_fixtures.logger', Logger::class)
        ->args([
            'sylius_fixtures',
            [service('sylius_fixtures.logger.handler.console')],
        ]);

    $services->set('sylius_fixtures.logger.handler.console', ConsoleHandler::class)
        ->args([
            null,
            true,
            ['32' => Level::Notice, '64' => Level::Info],
        ])
        ->tag('kernel.event_subscriber')
        ->call('setFormatter', [service('sylius_fixtures.logger.formatter.console')]);

    $services->set('sylius_fixtures.logger.formatter.console', ConsoleFormatter::class)
        ->args([['format' => '%%message%% %%context%% %%extra%%
']]);
};
