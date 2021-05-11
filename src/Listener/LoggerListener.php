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

namespace Sylius\Bundle\FixturesBundle\Listener;

use Psr\Log\LoggerInterface;

final class LoggerListener extends AbstractListener implements BeforeSuiteListenerInterface, BeforeFixtureListenerInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /** @param array<mixed> $options */
    public function beforeSuite(SuiteEvent $suiteEvent, array $options): void
    {
        $this->logger->notice(sprintf('Running suite "%s"…', $suiteEvent->suite()->getName()));
    }

    /** @param array<mixed> $options */
    public function beforeFixture(FixtureEvent $fixtureEvent, array $options): void
    {
        $this->logger->notice(sprintf('Running fixture "%s"…', $fixtureEvent->fixture()->getName()));
    }

    public function getName(): string
    {
        return 'logger';
    }
}
