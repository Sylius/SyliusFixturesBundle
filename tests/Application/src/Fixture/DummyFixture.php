<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Fixture;

use Sylius\Bundle\FixturesBundle\Fixture\AbstractExampleFactoryAwareFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class DummyFixture extends AbstractExampleFactoryAwareFixture
{
    public function getName(): string
    {
        return 'dummy';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('foo')->cannotBeEmpty()->end()
        ;
    }
}
