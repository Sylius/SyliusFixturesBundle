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

namespace App\Fixture\Factory;

use App\Entity\Bar;
use App\Entity\Foo;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\FixturesBundle\Fixture\OptionsResolver\LazyOption;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FooExampleFactory extends AbstractExampleFactory
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    public function create(array $options = []): Foo
    {
        $foo = new Foo();
        $foo->setBar($options['bar']);

        return $foo;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('bar', LazyOption::randomOne(
            $this->entityManager->getRepository(Bar::class)
        ));
    }
}
