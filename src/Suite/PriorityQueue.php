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
    private array $records = [];

    private bool $sorted = false;

    public function insert(array $data, int $priority = 0): void
    {
        $this->records[] = ['priority' => $priority, 'data' => $data];
        $this->sorted = false;
    }

    public function getIterator(): \Traversable
    {
        if ($this->sorted === false) {
            /** @psalm-suppress InvalidPassByReference Doing PHP magic, it works this way */
            array_multisort(
                array_column($this->records, 'priority'),
                \SORT_DESC,
                array_keys($this->records),
                \SORT_ASC,
                $this->records,
            );

            $this->sorted = true;
        }

        foreach ($this->records as $record) {
            yield $record['data'];
        }
    }
}
