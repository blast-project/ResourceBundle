<?php

/*
 * This file is part of the Blast Project package.
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Blast\Bundle\ResourceBundle\Metadata\Metadata;

class BlastResourceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([],
                        $container), $configs);
        $loader = new YamlFileLoader($container,
                new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        if (array_key_exists('resources', $config)) {
            $this->loadResources($config['resources'], $container);
        }

        if (array_key_exists('underscored_bundle_prefix_strategy', $config)) {
            $this->configureUnderscoredBundlePrefixStrategy(
                    $config['underscored_bundle_prefix_strategy'], $container);
        }
    }

    private function loadResources(array $resources, ContainerBuilder $container)
    {
        $resources = $container->hasParameter('blast.resources') ? $container->getParameter('blast.resources') : [];

        foreach ($resources as $alias => $resourceParameters) {
            $metadata = Metadata::fromAliasAndParameters($alias,
                            $resourceParameters);
            $resources = array_merge($resources, [$alias => $resourceParameters]);
        }

        $container->setParameter('blast.resources', $resources);
    }

    private function configureUnderscoredBundlePrefixStrategy(array $config,
            ContainerBuilder $container)
    {
        $definition = $container->getDefinition('blast.resource.doctrine.orm.naming_strategy.underscored_bundle_prefix');
        $args = $definition->getArguments();
        $config['fallback'] = new Reference($config['fallback']);
        $args[1] = $config;
        $definition->setArguments($args);
    }
}
