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
use Sylius\Bundle\FixturesBundle\Fixture\AbstractExampleFactoryAwareFixture;
use Sylius\Bundle\FixturesBundle\Fixture\Factory\ExampleFactoryInterface;

final class FooFixture extends AbstractExampleFactoryAwareFixture
{
    public function getName(): string
    {
        return 'foo';
    }

    protected function getExampleFactory(): ExampleFactoryInterface
    {
        return new FooExampleFactory($this->entityManager);
    }
}
