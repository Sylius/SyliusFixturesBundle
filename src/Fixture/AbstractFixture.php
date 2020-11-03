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

namespace Sylius\Bundle\FixturesBundle\Fixture;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

abstract class AbstractFixture implements FixtureInterface
{
    final public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->getName());

        /** @var ArrayNodeDefinition $optionsNode */
        $optionsNode = $treeBuilder->getRootNode();

        $this->configureOptionsNode($optionsNode);

        return $treeBuilder;
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        // empty
    }
}
