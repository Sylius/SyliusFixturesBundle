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

namespace spec\Sylius\Bundle\FixturesBundle\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Listener\SuiteLoaderListener;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;

final class SuiteLoaderListenerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SuiteLoaderListener::class);
    }

    public function let(SuiteRegistryInterface $suiteRegistry, SuiteLoaderInterface $suiteLoader): void
    {
        $this->beConstructedWith($suiteRegistry, $suiteLoader);
    }

    public function it_has_a_name(): void
    {
        $this->getName()->shouldReturn('suite_loader');
    }

    public function it_loads_all_fixtures_configured(
        SuiteRegistryInterface $suiteRegistry,
        SuiteLoaderInterface $suiteLoader,
        SuiteInterface $suite,
    ): void {
        $suiteRegistry->getSuite('other_suits')->willReturn($suite);
        $suiteLoader->load($suite)->shouldBeCalled();

        $suiteEvent = new SuiteEvent($suite->getWrappedObject());

        $this->beforeSuite($suiteEvent, ['suites' => ['other_suits']]);
    }
}
