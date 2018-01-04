<?php

/*
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
use Blast\Bundle\ResourceBundle\Doctrine\ORM\Repository\ResourceRepository;
use Blast\Component\Resource\Metadata\Metadata;
use Blast\Component\Resource\Metadata\MetadataInterface;

class BlastResourceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');
        if (array_key_exists('resources', $config)) {
            $this->loadResources($config['resources'], $container);
        }
    }

    private function loadResources(array $resources, ContainerBuilder $container)
    {
        foreach ($resources as $alias => $resourceParameters) {
            $resources = array_merge($resources, [$alias => $resourceParameters]);
            $metadata = Metadata::createFromAliasAndParameters($alias, $resourceParameters);
            $this->declareModelParameter($container, $metadata);
            $this->declareRepositoryParameter($container, $metadata);
        }

        $container->setParameter('blast.resources', $resources);
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    private function declareModelParameter(ContainerBuilder $container, MetadataInterface $metadata)
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
    private function declareRepositoryParameter(ContainerBuilder $container, MetadataInterface $metadata)
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
}
