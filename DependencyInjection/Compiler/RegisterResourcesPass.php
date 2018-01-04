<?php

/*
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Blast\Component\Resource\Metadata\Metadata;
use Blast\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Blast\Bundle\ResourceBundle\Doctrine\ORM\Repository\ResourceRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 */
class RegisterResourcesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        try {
            $resources = $container->getParameter('blast.resources');
            $registry = $container->findDefinition('blast.resource_registry');
            $targetEntityResolver = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
        } catch (InvalidArgumentException $exception) {
            return;
        }

        foreach ($resources as $alias => $parameters) {
            $metadata = Metadata::createFromAliasAndParameters($alias, $parameters);
            //object arguments cannot be passed to "addMethodCall"
            $registry->addMethodCall('addFromAliasAndParameters', [$alias, $parameters]);

            $this->declareRepositoryService($container, $metadata);
            $this->resolveTargetEntities($targetEntityResolver, $container, $metadata);

            if (!$targetEntityResolver->hasTag('doctrine.event_listener')) {
                $targetEntityResolver->addTag('doctrine.event_listener', ['event' => 'loadClassMetadata']);
            }
        }
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function declareRepositoryService(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $repositoryAlias = sprintf('%s.repository.%s', $metadata->getPrefix(), $metadata->getAlias());
        $repositoryParameter = sprintf('%s.class', $repositoryAlias);
        $repositoryClass = ResourceRepository::class;

        if ($container->hasParameter($repositoryParameter)) {
            $repositoryClass = $container->getParameter($repositoryParameter);
        }

        $definition = new Definition($repositoryClass);
        $definition->setArguments([
          new Reference('doctrine.orm.entity_manager'),
          $this->getClassMetadataDefinition($metadata),
        ]);
        $container->setDefinition($repositoryAlias, $definition);
    }

    /**
     * @param Definition        $targetEntityResolver
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function resolveTargetEntities(Definition $targetEntityResolver, ContainerBuilder $container, MetadataInterface $metadata)
    {
        $classMap = $metadata->getClassMap();
        $modelClass = $classMap->getModel();
        $interfaces = $classMap->getInterfaces();

        foreach ($interfaces as $interface) {
            $targetEntityResolver->addMethodCall('addResolveTargetEntity', [
                $interface, $modelClass, [],
            ]);
        }
    }

    /**
     * @param MetadataInterface $metadata
     */
    protected function getClassMetadataDefinition(MetadataInterface $metadata): Definition
    {
        $definition = new Definition(ClassMetadata::class);
        $definition
          ->setFactory([new Reference('doctrine.orm.entity_manager'), 'getClassMetadata'])
          ->setArguments([$metadata->getClassMap()->getModel()])
          ->setPublic(false);

        return $definition;
    }
}
