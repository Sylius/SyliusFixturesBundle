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

namespace Sylius\Bundle\FixturesBundle\Suite;

/**
 * @internal
 */
final class PriorityQueue implements \IteratorAggregate
{
    /**
     * @psalm-var array<int, array{data: array, priority: int}>
     *
     * @var array[]
     */
    private $records = [];

    /**
     * @psalm-var array<int, array>
     *
     * @var array[]
     */
    private $sortedRecords = [];

    /** @var bool */
    private $sorted = false;

    public function insert(array $data, int $priority = 0): void
    {
        $this->records[] = ['priority' => $priority, 'data' => $data];
        $this->sorted = false;
    }

    public function getIterator(): \Traversable
    {
        if ($this->sorted === false) {
            // Reversing the records to maintain FIFO order for item with the same priority
            $this->sortedRecords = array_reverse($this->records);

            /** @psalm-suppress InvalidPassByReference Doing PHP magic, it works this way */
            array_multisort(array_column($this->sortedRecords, 'priority'), \SORT_DESC, $this->sortedRecords);

            $this->sorted = true;
        }

        foreach ($this->sortedRecords as $record) {
            yield $record['data'];
        }
    }
}
