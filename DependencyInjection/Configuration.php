<?php

/*
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
            ->arrayNode('resources')
                ->useAttributeAsKey('name')
                ->prototype('array')
                  ->children()
                    ->arrayNode('classes')
                      ->isRequired()
                      ->addDefaultsIfNotSet()
                      ->children()
                        ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('repository')->cannotBeEmpty()->end()
                        ->arrayNode('interfaces')->scalarPrototype()->end()
                      ->end()
                   ->end()

                ->end()
            ->end()
         ->end();

        return $treeBuilder;
    }
}
