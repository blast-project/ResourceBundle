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
use Symfony\Component\Config\Loader\LoaderInterface;
use Blast\Bundle\ResourceBundle\Metadata\Metadata;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BlastResourceExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([],
                        $container), $configs);
        $loader = new YamlFileLoader($container,
                new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        if ( array_key_exists('resources', $config) ) {
            $this->loadResources($config['resources'], $container);
        }
    }

    private function loadResources(array $resources, ContainerBuilder $container)
    {
        $resources = $container->hasParameter('blast.resources') ? $container->getParameter('blast.resources') : [];

        foreach ( $resources as $alias => $resourceParameters ) {
            $metadata = Metadata::fromAliasAndParameters($alias,
                            $resourceParameters);
            $resources = array_merge($resources, [$alias => $resourceParameters]);
        }

        $container->setParameter('blast.resources', $resources);
    }

}
