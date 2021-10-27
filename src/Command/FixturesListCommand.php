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

namespace Sylius\Bundle\FixturesBundle\Command;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixturesListCommand extends Command
{
    private SuiteRegistryInterface $suiteRegistry;

    private FixtureRegistryInterface $fixtureRegistry;

    public function __construct(SuiteRegistryInterface $suiteRegistry, FixtureRegistryInterface $fixtureRegistry)
    {
        parent::__construct(null);

        $this->suiteRegistry = $suiteRegistry;
        $this->fixtureRegistry = $fixtureRegistry;
    }

    protected function configure(): void
    {
        $this
            ->setName('sylius:fixtures:list')
            ->setDescription('Lists available fixtures')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->listSuites($output);
        $this->listFixtures($output);

        return 0;
    }

    private function listSuites(OutputInterface $output): void
    {
        $suites = $this->suiteRegistry->getSuites();

        $output->writeln('Available suites:');

        foreach ($suites as $suite) {
            $output->writeln(' - ' . $suite->getName());
        }
    }

    private function listFixtures(OutputInterface $output): void
    {
        $fixtures = $this->fixtureRegistry->getFixtures();

        $output->writeln('Available fixtures:');

        foreach ($fixtures as $name => $fixture) {
            $output->writeln(' - ' . $name);
        }
    }
}
