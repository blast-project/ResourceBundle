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
use Blast\Component\Resource\Metadata\ClassMap;
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
        } catch (InvalidArgumentException $exception) {
            return;
        }

        foreach ($resources as $alias => $parameters) {
            $metadata = new Metadata($alias, ClassMap::fromArray($parameters['classes']));
            //object arguments cannot be passed to "addMethodCall"
            $registry->addMethodCall('addFromAliasAndParameters', [$alias, $parameters]);

            $this->declareModelParameter($container, $metadata);
            $this->declareRepositoryParameter($container, $metadata);
            $this->declareRepositoryService($container, $metadata);
        }
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function declareModelParameter(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $classMap = $metadata->getClassMap();

        if (!class_exists($classMap->getModel())) {
            throw new \InvalidArgumentException(sprintf(
                    'Resource "%s" declare a non-existent model class.', $metadata->getAlias()
                ));
        }
        $modelAlias = sprintf('sil.model.%s', $metadata->getAlias());
        $container->setParameter($modelAlias . '.class', $classMap->getModel());
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function declareRepositoryParameter(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $classMap = $metadata->getClassMap();
        $repositoryClass = ResourceRepository::class;
        $repositoryAlias = sprintf('sil.repository.%s', $metadata->getAlias());

        if ($classMap->hasRepository()) {
            $repositoryClass = $classMap->getRepository();
        }

        if (!class_exists($repositoryClass)) {
            throw new \InvalidArgumentException(sprintf(
                    'Resource "%s" declare a non-existent repository class.',
                    $metadata->getAlias()
                ));
        }

        $container->setParameter($repositoryAlias . '.class', $repositoryClass);
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function declareRepositoryService(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $repositoryAlias = sprintf('sil.repository.%s', $metadata->getAlias());
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
