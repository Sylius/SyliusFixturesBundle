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

namespace App\Fixture;

use App\Entity\Bar;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;

final class BarFixture extends AbstractFixture
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getName(): string
    {
        return 'bar';
    }

    public function load(array $options): void
    {
        $firstBar = new Bar();
        $this->entityManager->persist($firstBar);

        $secondBar = new Bar();
        $this->entityManager->persist($secondBar);

        $this->entityManager->flush();
    }
}
