<?php

/*
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU GPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait ResourceExtensionTrait
{
    /**
     * @param string           $applicationName
     * @param string           $driver
     * @param array            $resources
     * @param ContainerBuilder $container
     */
    protected function registerResources(array $resources, ContainerBuilder $container)
    {
        foreach ($resources as $alias => $resourceConfig) {
            $resources = $container->hasParameter('blast.resources') ? $container->getParameter('blast.resources') : [];
            $resources = array_merge($resources, [$alias => $resourceConfig]);
            $container->setParameter('blast.resources', $resources);
        }
    }
}
