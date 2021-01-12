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

namespace Sylius\Bundle\FixturesBundle\Tests\Fixture;

use App\Fixture\DummyFixture;
use App\Fixture\Factory\DummyExampleFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ExampleWithFactoryTest extends KernelTestCase
{
    public function testExampleFactoryAwareFixture()
    {
        self::bootKernel();

        $container = self::$container;

        $this->assertTrue($container->has(DummyFixture::class));
    }

    public function testExampleFactory()
    {
        self::bootKernel();

        $container = self::$container;

        $this->assertTrue($container->has(DummyExampleFactory::class));
    }

    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('sylius:fixtures:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--no-interaction' => true,
        ], [
            'interactive' => false,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
