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

namespace Sylius\Bundle\FixturesBundle\Tests\Listener;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\FixturesBundle\Listener\SuiteLoaderListener;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class SuiteLoaderListenerTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function by_default_it_has_an_empty_suite_list(): void
    {
        $this->assertProcessedConfigurationEquals([[]], ['suites' => []], 'suites');
    }

    public function it_processes_the_suites(): void
    {
        $this->assertProcessedConfigurationEquals([['suites' => ['abc']]], [['suites' => ['abc']]], 'suites');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new SuiteLoaderListener(
            $this->getMockBuilder(SuiteRegistryInterface::class)->getMock(),
            $this->getMockBuilder(SuiteLoaderInterface::class)->getMock(),
        );
    }
}
