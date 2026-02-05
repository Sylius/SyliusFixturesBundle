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

use Sylius\Bundle\FixturesBundle\Suite\LazySuiteRegistry;
use Sylius\Bundle\FixturesBundle\Suite\SuiteFactory;
use Sylius\Bundle\FixturesBundle\Suite\SuiteFactoryInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Component\Config\Definition\Processor;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_fixtures.suite_factory', SuiteFactory::class)
        ->private()
        ->args([
            service('sylius_fixtures.fixture_registry'),
            service('sylius_fixtures.listener_registry'),
            inline_service(Processor::class),
        ]);

    $services->alias(SuiteFactoryInterface::class, 'sylius_fixtures.suite_factory');

    $services->set('sylius_fixtures.suite_registry', LazySuiteRegistry::class)
        ->args([service('sylius_fixtures.suite_factory')]);

    $services->alias(SuiteRegistryInterface::class, 'sylius_fixtures.suite_registry');
};
