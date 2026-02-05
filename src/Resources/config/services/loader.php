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

use Sylius\Bundle\FixturesBundle\Loader\FixtureLoader;
use Sylius\Bundle\FixturesBundle\Loader\FixtureLoaderInterface;
use Sylius\Bundle\FixturesBundle\Loader\HookableFixtureLoader;
use Sylius\Bundle\FixturesBundle\Loader\HookableSuiteLoader;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoader;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_fixtures.fixture_loader', HookableFixtureLoader::class)
        ->args([inline_service(FixtureLoader::class)]);

    $services->alias(FixtureLoaderInterface::class, 'sylius_fixtures.fixture_loader');

    $services->set('sylius_fixtures.suite_loader', HookableSuiteLoader::class)
        ->args([inline_service(SuiteLoader::class)
            ->args([service('sylius_fixtures.fixture_loader')])]);

    $services->alias(SuiteLoaderInterface::class, 'sylius_fixtures.suite_loader');
};
