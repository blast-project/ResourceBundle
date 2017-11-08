<?php

declare(strict_types=1);

/*
 * This file is part of the Sil Project.
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU GPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Doctrine\ORM\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Common\Persistence\Mapping\RuntimeReflectionService;
use Blast\Bundle\ResourceBundle\Metadata\MetadataRegistryInterface;

/**
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 */
final class MappedSuperClassSubscriber implements EventSubscriber
{
    /**
     * @var MetadataRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var RuntimeReflectionService
     */
    private $reflectionService;

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function __construct(MetadataRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();
        $this->convertToEntityIfNeeded($metadata,
                $eventArgs->getEntityManager()->getConfiguration());

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata,
                    $eventArgs->getEntityManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function convertToEntityIfNeeded(ClassMetadataInfo $metadata,
            Configuration $configuration)
    {
        if (false === $metadata->isMappedSuperclass) {
            return;
        }

        try {
            $resourceMetadata = $this->resourceRegistry->getByClass($metadata->getName());
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if ($metadata->getName() === $resourceMetadata->getClass('entity')) {
            $metadata->isMappedSuperclass = false;
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param Configuration     $configuration
     */
    private function setAssociationMappings(ClassMetadataInfo $metadata,
            Configuration $configuration)
    {
        $class = $metadata->getName();
        if (!class_exists($class)) {
            return;
        }
        foreach (class_parents($class) as $parent) {
            if (false === in_array($parent,
                            $configuration->getMetadataDriverImpl()->getAllClassNames(),
                            true)) {
                continue;
            }
            $parentMetadata = new ClassMetadata(
                    $parent, $configuration->getNamingStrategy()
            );
            // Wakeup Reflection
            $parentMetadata->wakeupReflection($this->getReflectionService());
            // Load Metadata
            $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent,
                    $parentMetadata);
            if (false === $this->isResource($parentMetadata)) {
                continue;
            }
            if ($parentMetadata->isMappedSuperclass) {
                foreach ($parentMetadata->getAssociationMappings() as $key => $value) {
                    if ($this->isRelation($value['type']) && !isset($metadata->associationMappings[$key])) {
                        $metadata->associationMappings[$key] = $value;
                    }
                }
            }
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        if (false === $this->isResource($metadata)) {
            return;
        }
        foreach ($metadata->getAssociationMappings() as $key => $value) {
            if ($this->isRelation($value['type'])) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }

    /**
     * @param int $type
     *
     * @return bool
     */
    private function isRelation($type)
    {
        return in_array(
                $type,
                [
                    ClassMetadataInfo::MANY_TO_MANY,
                    ClassMetadataInfo::ONE_TO_MANY,
                    ClassMetadataInfo::ONE_TO_ONE,
                ], true
        );
    }

    private function isResource(ClassMetadataInfo $metadata)
    {
        if (!$reflClass = $metadata->getReflectionClass()) {
            return false;
        }

        return true;
    }

    private function getReflectionService()
    {
        if ($this->reflectionService === null) {
            $this->reflectionService = new RuntimeReflectionService();
        }

        return $this->reflectionService;
    }
}
