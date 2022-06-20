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

namespace Sylius\Bundle\FixturesBundle\Tests\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Command\FixturesLoadCommand;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistry;
use Sylius\Bundle\FixturesBundle\Suite\LazySuiteRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class FixtureListCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected static $container;

    public function setUp(): void
    {
        self::$kernel = static::createKernel();
        self::$kernel->boot();
        self::$container = self::$kernel->getContainer();

        /** @var FixtureRegistry $fixtureRegistry */
        $fixtureRegistry = self::$container->get('sylius_fixtures.fixture_registry');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $fixtureRegistry->addFixture(new SampleFixture($entityManager));

        /** @var LazySuiteRegistry $suiteRegistry */
        $suiteRegistry = self::$container->get('sylius_fixtures.suite_registry');
        $suiteRegistry
            ->addSuite('default', [
                'fixtures' => $this->createFixture('sample_fixture'),
                'listeners' => [],
            ])
        ;

        $application = new Application(self::$kernel);
        $application->add(new FixturesLoadCommand(
            $suiteRegistry,
            self::$container->get('sylius_fixtures.suite_loader'),
            self::$container->getParameter('kernel.environment'),
        ));
        $command = $application->find('sylius:fixtures:list');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @test
     */
    public function it_lists_the_available_fixtures(): void
    {
        $this->commandTester->execute([]);

        $this->assertSame(
            'Available suites:
 - default
Available fixtures:
 - sample_fixture
',
            $this->commandTester->getDisplay(true),
        );
    }

    private function createFixture(string $name, array $options = []): array
    {
        return [
            $name => [
                'name' => $name,
                'options' => $options,
            ],
        ];
    }
}
