<?php
declare(strict_types=1);

namespace Sylius\Bundle\FixturesBundle\Tests\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Command\FixturesLoadCommand;
use Sylius\Bundle\FixturesBundle\Suite\SuiteNotFoundException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class FixtureLoaderTest extends KernelTestCase
{
    /** @var CommandTester */
    private $commandTester;

    /** @var EntityManagerInterface */
    private $em;

    protected function setUp()
    {
        self::$kernel = static::createKernel();
        self::$kernel->boot();
        self::$container = self::$kernel->getContainer();

        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
        $connection = $this->em->getConnection();

        $connection->exec('CREATE TABLE IF NOT EXISTS testTable (test_column varchar(255) NOT NULL);');
        $connection->exec('DELETE FROM testTable');

        self::$container
            ->get('sylius_fixtures.fixture_registry')
            ->addFixture(new SampleFixture($this->em))
        ;

        $application = new Application(self::$kernel);
        $application->add(new FixturesLoadCommand());
        $command = $application->find('sylius:fixtures:load');
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
    public function loads_default_suite_if_none_is_defined(): void
    {
        self::$container
            ->get('sylius_fixtures.suite_registry')
            ->addSuite('default', [
                'fixtures'  => $this->createFixture('sample_fixture'),
                'listeners' => [],
            ])
        ;

        $this->commandTester->execute([], ['interactive' => false]);

        $connection = $this->em->getConnection();
        $result = $connection->fetchArray('SELECT count(*) FROM testTable WHERE test_column = "test";');
        $this->assertSame(1, (int) $result[0]);
    }

    /**
     * @test
     */
    public function it_fails_if_no_default_suite_exist(): void
    {
        $this->expectException(SuiteNotFoundException::class);

        $this->commandTester->execute([], ['interactive' => false]);
    }

}


