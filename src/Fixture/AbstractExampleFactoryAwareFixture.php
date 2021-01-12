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

namespace Sylius\Bundle\FixturesBundle\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\Factory\ExampleFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractExampleFactoryAwareFixture implements FixtureInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ExampleFactoryInterface|null */
    private $exampleFactory;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(EntityManagerInterface $entityManager, ExampleFactoryInterface $exampleFactory = null)
    {
        $this->entityManager = $entityManager;
        $this->exampleFactory = $exampleFactory;

        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('random', 0)
                ->setAllowedTypes('random', 'int')
                ->setDefault('prototype', [])
                ->setAllowedTypes('prototype', 'array')
                ->setDefault('custom', [])
                ->setAllowedTypes('custom', 'array')
                ->setNormalizer('custom', function (Options $options, array $custom) {
                    if ($options['random'] <= 0) {
                        return $custom;
                    }

                    return array_merge($custom, array_fill(0, $options['random'], $options['prototype']));
                })
        ;
    }

    final public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $i = 0;
        foreach ($options['custom'] as $resourceOptions) {
            $resolvedOptions = $this->getExampleFactory()->resolveOptions($resourceOptions);
            $resource = $this->getExampleFactory()->create($resolvedOptions);

            $this->entityManager->persist($resource);

            ++$i;

            if (0 === ($i % 10)) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     * {@inheritdoc}
     */
    final public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->getName());

        /** @var ArrayNodeDefinition $optionsNode */
        $optionsNode = $treeBuilder->getRootNode();

        $nodeBuilder = $optionsNode->children();
        $nodeBuilder->integerNode('random')->min(0)->defaultValue(0)->end();
        $nodeBuilder->variableNode('prototype')->end();

        /** @var ArrayNodeDefinition $resourcesNode */
        $resourcesNode = $optionsNode->children()->arrayNode('custom');

        /** @var ArrayNodeDefinition $resourceNode */
        $resourceNode = $resourcesNode->requiresAtLeastOneElement()->arrayPrototype();
        $this->configureResourceNode($resourceNode);

        return $treeBuilder;
    }

    protected function getExampleFactory(): ExampleFactoryInterface
    {
        if (null === $this->exampleFactory) {
            $exampleFactory = $this->createExampleFactory();

            if (null === $exampleFactory) {
                throw new \LogicException(sprintf('No example Factory class was found. Use %s or override getExampleFactoryClass method', $this->getExampleFactoryClass()));
            }

            if (!$exampleFactory instanceof ExampleFactoryInterface) {
                throw new \LogicException(sprintf('Example Factory "%s" must implement %s.', \get_class($exampleFactory), ExampleFactoryInterface::class));
            }

            $this->exampleFactory = $exampleFactory;
        }

        return $this->exampleFactory;
    }

    protected function getExampleFactoryClass(): string
    {
        $names = explode('\\', static::class);
        $name = end($names);
        $basename = preg_replace('/(.+)Fixture$/', '${1}', $name);

        $pos = strrpos(static::class, '\\');
        $namespace = false === $pos ? '' : substr(static::class, 0, $pos);

        return $namespace . '\\Factory\\' . $basename . 'ExampleFactory';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        // empty
    }

    private function createExampleFactory(): ?object
    {
        $class = $this->getExampleFactoryClass();

        return class_exists($class) ? new $class() : null;
    }
}
