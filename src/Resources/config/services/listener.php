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

use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistry;
use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistryInterface;
use Sylius\Bundle\FixturesBundle\Listener\LoggerListener;
use Sylius\Bundle\FixturesBundle\Listener\SuiteLoaderListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius_fixtures.listener_registry', ListenerRegistry::class)
        ->private();

    $services->alias(ListenerRegistryInterface::class, 'sylius_fixtures.listener_registry');

    $services->set('sylius_fixtures.listener.suite_loader_listener', SuiteLoaderListener::class)
        ->args([
            service('sylius_fixtures.suite_registry'),
            service('sylius_fixtures.suite_loader'),
        ])
        ->tag('sylius_fixtures.listener');

    $services->set('sylius_fixtures.listener.logger', LoggerListener::class)
        ->private()
        ->args([service('sylius_fixtures.logger')])
        ->tag('sylius_fixtures.listener');
};
