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

namespace Sylius\Bundle\FixturesBundle\Command;

use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(name: 'sylius:fixtures:load', description: 'Loads fixtures from given suite')]
final class FixturesLoadCommand extends Command
{
    public function __construct(
        private SuiteRegistryInterface $suiteRegistry,
        private SuiteLoaderInterface $suiteLoader,
        private string $environment,
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('suite', InputArgument::OPTIONAL, 'Suite name', 'default')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->isInteractive()) {
            /** @var QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');

            $output->writeln(sprintf(
                "\n<error>Warning! Loading fixtures may purge your database for the %s environment (if `orm_purger` is used in your suite).</error>\n",
                $this->environment,
            ));

            if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Continue? (y/N) ', false))) {
                return 1;
            }
        }

        $this->loadSuites($input);

        return 0;
    }

    private function loadSuites(InputInterface $input): void
    {
        $suiteName = $input->getArgument('suite');

        assert(is_string($suiteName));

        $suite = $this->suiteRegistry->getSuite($suiteName);

        $this->suiteLoader->load($suite);
    }
}
