<?php

/*
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('blast_resource');
        $rootNode
                ->children()
                    ->arrayNode('resources')->end()
                    ->arrayNode('underscored_bundle_prefix_strategy')->children()
                        ->scalarNode('fallback')
                            ->defaultValue('doctrine.orm.naming_strategy.underscore')
                        ->end()
                        ->arrayNode('filter')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
