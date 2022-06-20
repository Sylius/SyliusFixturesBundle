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
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\FixturesBundle\Command\FixturesLoadCommand;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistry;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Suite\LazySuiteRegistry;
use Sylius\Bundle\FixturesBundle\Suite\SuiteNotFoundException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Webmozart\Assert\Assert;

final class FixtureLoaderTest extends KernelTestCase
{
    private CommandTester $commandTester;

    private EntityManagerInterface $em;

    /** @var MockObject|LoggerInterface */
    private $logger;

    protected static $container;

    public function getLazySuiteRegistry(): LazySuiteRegistry
    {
        /** @var LazySuiteRegistry $lazySuiteRegistry */
        $lazySuiteRegistry = self::$container->get('sylius_fixtures.suite_registry');

        return $lazySuiteRegistry;
    }

    public function setUp(): void
    {
        self::$kernel = static::createKernel();
        self::$kernel->boot();
        self::$container = self::$kernel->getContainer();

        $this->logger = $this->createMock(LoggerInterface::class);
        self::$container->set('sylius_fixtures.logger', $this->logger);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $this->em = $entityManager;
        $connection = $this->em->getConnection();

        $connection->executeStatement('CREATE TABLE IF NOT EXISTS testTable (test_column varchar(255) NOT NULL);');
        $connection->executeStatement('DELETE FROM testTable');

        /** @var FixtureRegistry $registry */
        $registry = self::$container->get('sylius_fixtures.fixture_registry');
        Assert::isInstanceOf($registry, FixtureRegistryInterface::class);
        $registry->addFixture(new SampleFixture($this->em));

        $application = new Application(self::$kernel);
        $application->add(new FixturesLoadCommand(
            self::$container->get('sylius_fixtures.suite_registry'),
            self::$container->get('sylius_fixtures.suite_loader'),
            self::$container->getParameter('kernel.environment'),
        ));
        $command = $application->find('sylius:fixtures:load');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @test
     */
    public function loads_default_suite_if_none_is_defined(): void
    {
        $this->getLazySuiteRegistry()
            ->addSuite('default', [
                'fixtures' => $this->createConfiguration('sample_fixture'),
                'listeners' => [],
            ])
        ;

        $this->commandTester->execute([], ['interactive' => false]);

        $connection = $this->em->getConnection();
        $result = $connection->fetchAssociative('SELECT count(*) as amount FROM testTable WHERE test_column = "test";');
        $this->assertSame(1, (int) $result['amount']);
    }

    /**
     * @test
     */
    public function it_loads_the_specified_suite(): void
    {
        $this->getLazySuiteRegistry()
            ->addSuite('sample', [
                'fixtures' => $this->createConfiguration('sample_fixture'),
                'listeners' => [],
            ])
        ;

        $this->commandTester->execute(['suite' => 'sample'], ['interactive' => false]);

        $connection = $this->em->getConnection();
        $result = $connection->fetchAssociative('SELECT count(*) as amount FROM testTable WHERE test_column = "test";');
        $this->assertSame(1, (int) $result['amount']);
    }

    /**
     * @test
     */
    public function it_loads_the_suite_with_listeners(): void
    {
        $this->getLazySuiteRegistry()
             ->addSuite('sample', [
                 'fixtures' => $this->createConfiguration('sample_fixture'),
                 'listeners' => $this->createConfiguration('logger'),
             ])
        ;

        /** @var MockObject $logger */
        $logger = $this->logger;
        $logger->expects($this->exactly(2))->method('notice')->withAnyParameters();

        $this->commandTester->execute(['suite' => 'sample'], ['interactive' => false]);
    }

    /**
     * @test
     */
    public function it_loads_the_suite_with_suite_loader_listener(): void
    {
        $this->getLazySuiteRegistry()
            ->addSuite('default', [
                'fixtures' => $this->createConfiguration('sample_fixture'),
                'listeners' => [],
            ])
        ;
        $this->getLazySuiteRegistry()
            ->addSuite('sample', [
                 'fixtures' => $this->createConfiguration('sample_fixture'),
                 'listeners' => $this->createConfiguration('suite_loader', ['options' => ['suites' => ['default']]]),
             ])
        ;

        $this->commandTester->execute(['suite' => 'sample'], ['interactive' => false]);

        $connection = $this->em->getConnection();
        $result = $connection->fetchAssociative('SELECT count(*) as amount FROM testTable WHERE test_column = "test";');
        $this->assertSame(2, (int) $result['amount']);
    }

    /**
     * @test
     */
    public function it_fails_if_no_default_suite_exist(): void
    {
        $this->expectException(SuiteNotFoundException::class);

        $this->commandTester->execute([], ['interactive' => false]);
    }

    private function createConfiguration(string $name, array $options = []): array
    {
        return [
            $name => [
                'name' => $name,
                'options' => $options,
            ],
        ];
    }
}
