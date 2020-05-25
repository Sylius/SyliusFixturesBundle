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

namespace spec\Sylius\Bundle\FixturesBundle\Suite;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FixturesBundle\Suite\PriorityQueue;

final class PriorityQueueSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(PriorityQueue::class);
    }

    function it_keeps_fifo_order_for_elements_with_same_priority(): void
    {
        $this->insert(['element' => 2]);
        $this->insert(['element' => 1]);
        $this->insert(['element' => 3]);

        $this->getIterator()->shouldIterateAs([['element' => 2], ['element' => 1], ['element' => 3]]);
    }

    function it_sorts_elements_by_their_priority(): void
    {
        $this->insert(['element' => 1], -1);
        $this->insert(['element' => 2], 0);
        $this->insert(['element' => 3], 1);
        $this->insert(['element' => 4], 0);

        $this->getIterator()->shouldIterateAs([['element' => 3], ['element' => 2], ['element' => 4], ['element' => 1]]);
    }
}
