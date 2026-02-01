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

use Sylius\Bundle\FixturesBundle\Command\FixturesListCommand;
use Sylius\Bundle\FixturesBundle\Command\FixturesLoadCommand;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/fixture.php');
    $container->import('services/listener.php');
    $container->import('services/loader.php');
    $container->import('services/logger.php');
    $container->import('services/suite.php');

    $services->set(FixturesListCommand::class)
        ->args([
            service('sylius_fixtures.suite_registry'),
            service('sylius_fixtures.fixture_registry'),
        ])
        ->tag('console.command');

    $services->set(FixturesLoadCommand::class)
        ->args([
            service('sylius_fixtures.suite_registry'),
            service('sylius_fixtures.suite_loader'),
            '%kernel.environment%',
        ])
        ->tag('console.command');
};
