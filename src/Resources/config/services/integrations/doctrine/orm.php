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

use Sylius\Bundle\FixturesBundle\Listener\ORMPurgerListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_fixtures.listener.orm_purger', ORMPurgerListener::class)
        ->private()
        ->args([service('doctrine')])
        ->tag('sylius_fixtures.listener');
};
