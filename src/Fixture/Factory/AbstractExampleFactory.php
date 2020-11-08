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

namespace Sylius\Bundle\FixturesBundle\Fixture\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var OptionsResolver */
    protected $optionsResolver;

    public function __construct()
    {
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function resolveOptions(array $options = []): array
    {
        return $this->optionsResolver->resolve($options);
    }

    abstract protected function configureOptions(OptionsResolver $resolver): void;
}
