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

use App\Entity\Dummy;
use Sylius\Bundle\FixturesBundle\Fixture\Factory\AbstractExampleFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DummyExampleFactory extends AbstractExampleFactory
{
    public function create(array $options = []): Dummy
    {
        $dummy = new Dummy();
        $dummy->setFoo($options['foo']);

        return $dummy;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('foo', 'bar');
    }
}
