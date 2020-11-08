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

use App\Fixture\Factory\FooExampleFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractExampleFactoryAwareFixture;

final class AlternativeFooFixture extends AbstractExampleFactoryAwareFixture
{
    public function __construct(EntityManagerInterface $entityManager, FooExampleFactory $exampleFactory = null)
    {
        parent::__construct($entityManager, $exampleFactory);
    }

    public function getName(): string
    {
        return 'alternative_foo';
    }
}
