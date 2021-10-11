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

namespace Sylius\Bundle\FixturesBundle\Listener;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ORMPurgerListener extends AbstractListener implements BeforeSuiteListenerInterface
{
    private ManagerRegistry $managerRegistry;

    private static array $purgeModes = [
        'delete' => ORMPurger::PURGE_MODE_DELETE,
        'truncate' => ORMPurger::PURGE_MODE_TRUNCATE,
    ];

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function beforeSuite(SuiteEvent $suiteEvent, array $options): void
    {
        foreach ($options['managers'] as $managerName) {
            /** @var EntityManagerInterface $manager */
            $manager = $this->managerRegistry->getManager($managerName);

            $purger = new ORMPurger($manager, $options['exclude']);
            $purger->setPurgeMode(static::$purgeModes[$options['mode']]);
            $purger->purge();
        }
    }

    public function getName(): string
    {
        return 'orm_purger';
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNodeBuilder = $optionsNode->children();

        $optionsNodeBuilder
            ->enumNode('mode')
                ->values(['delete', 'truncate'])
                ->defaultValue('delete')
        ;

        $optionsNodeBuilder
            ->arrayNode('managers')
                ->defaultValue([null])
                ->scalarPrototype()
        ;

        $optionsNodeBuilder
            ->arrayNode('exclude')
                ->defaultValue([])
                ->scalarPrototype()
        ;
    }
}
