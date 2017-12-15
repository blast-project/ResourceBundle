<?php

/*
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

trait ResourceConfigurationTrait
{
    private function addResourcesSection(ArrayNodeDefinition $rootNode)
    {
        $resourceNode = $rootNode
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children();

        $this->addResourceDefinitions($resourceNode);

        $resourceNode
            ->end()
                ->end()
                    ->end();
    }

    private function addResourceDefinition(NodeBuilder $node, string $resourceName, string $defaultClassName)
    {
        $node
            ->arrayNode($resourceName)
                ->addDefaultsIfNotSet()
                ->children()
                    ->variableNode('options')->end()
                    ->arrayNode('classes')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('entity')->defaultValue($defaultClassName)->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
