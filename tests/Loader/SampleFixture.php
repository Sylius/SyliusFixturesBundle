<?php
declare(strict_types=1);

namespace Sylius\Bundle\FixturesBundle\Tests\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;

final class SampleFixture extends AbstractFixture
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function load(array $options): void
    {
        $this->entityManager
            ->getConnection()
            ->prepare('INSERT INTO testTable VALUES (?);')
            ->execute(['test']);
    }

    public function getName(): string
    {
        return 'sample_fixture';
    }
}
