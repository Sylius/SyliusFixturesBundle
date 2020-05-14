<?php
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
    /** @var CommandTester */
    private $commandTester;

    protected static $container;

    protected function setUp()
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
                'fixtures'  => $this->createFixture('sample_fixture'),
                'listeners' => [],
            ])
        ;

        $application = new Application(self::$kernel);
        $application->add(new FixturesLoadCommand());
        $command = $application->find('sylius:fixtures:list');
        $this->commandTester = new CommandTester($command);
    }

    private function createFixture(string $name, array $options = []): array
    {
        return [
            $name => [
                'name'    => $name,
                'options' => $options,
            ],
        ];
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
            $this->commandTester->getDisplay(true)
        );
    }

}
