<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\FixturesBundle\DependencyInjection;

use Sylius\Bundle\FixturesBundle\DependencyInjection\Compiler\FixtureRegistryPass;
use Sylius\Bundle\FixturesBundle\DependencyInjection\Compiler\ListenerRegistryPass;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusFixturesExtension extends Extension implements PrependExtensionInterface
{
    /** @param array<mixed> $config */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }

    /** @param array<array<mixed>> $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.php');

        $this->registerSuites($config, $container);

        $container
            ->registerForAutoconfiguration(FixtureInterface::class)
            ->addTag(FixtureRegistryPass::FIXTURE_SERVICE_TAG)
        ;
        $container
            ->registerForAutoconfiguration(ListenerInterface::class)
            ->addTag(ListenerRegistryPass::LISTENER_SERVICE_TAG)
        ;
    }

    public function prepend(ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $extensionsNamesToConfigurationFiles = [
            'doctrine' => 'doctrine/orm.php',
            'doctrine_mongodb' => 'doctrine/mongodb-odm.php',
            'doctrine_phpcr' => 'doctrine/phpcr-odm.php',
        ];

        foreach ($extensionsNamesToConfigurationFiles as $extensionName => $configurationFile) {
            if (!$container->hasExtension($extensionName)) {
                continue;
            }

            $loader->load('services/integrations/' . $configurationFile);
        }
    }

    /** @param array{suites: array<string, array<mixed>>} $config */
    private function registerSuites(array $config, ContainerBuilder $container): void
    {
        $suiteRegistry = $container->findDefinition('sylius_fixtures.suite_registry');
        foreach ($config['suites'] as $suiteName => $suiteConfiguration) {
            $suiteRegistry->addMethodCall('addSuite', [$suiteName, $suiteConfiguration]);
        }
    }
}
