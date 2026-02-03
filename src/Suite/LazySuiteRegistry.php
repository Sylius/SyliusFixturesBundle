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

namespace Sylius\Bundle\FixturesBundle\Suite;

final class LazySuiteRegistry implements SuiteRegistryInterface
{
    /** @var array<string, array<mixed>> */
    private array $suiteDefinitions = [];

    /** @var array<string, SuiteInterface> */
    private array $suites = [];

    public function __construct(private SuiteFactoryInterface $suiteFactory)
    {
    }

    /** @param array<mixed> $configuration */
    public function addSuite(string $name, array $configuration): void
    {
        $this->suiteDefinitions[$name] = $configuration;
    }

    public function getSuite(string $name): SuiteInterface
    {
        if (isset($this->suites[$name])) {
            return $this->suites[$name];
        }

        if (!isset($this->suiteDefinitions[$name])) {
            throw new SuiteNotFoundException($name);
        }

        return $this->suites[$name] = $this->suiteFactory->createSuite($name, $this->suiteDefinitions[$name]);
    }

    public function getSuites(): array
    {
        $suites = [];
        foreach (array_keys($this->suiteDefinitions) as $name) {
            $suites[$name] = $this->getSuite($name);
        }

        return $suites;
    }
}
