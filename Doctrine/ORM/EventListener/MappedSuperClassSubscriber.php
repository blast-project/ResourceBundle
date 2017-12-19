<?php

declare(strict_types=1);

/*
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
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
use Doctrine\Common\Persistence\Mapping\ReflectionService;
use Doctrine\Common\Persistence\Mapping\RuntimeReflectionService;
use Blast\Component\Resource\Metadata\MetadataRegistryInterface;
use Blast\Component\Resource\Model\ResourceInterface;

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
        $configuration = $eventArgs->getEntityManager()->getConfiguration();

        $this->processMetadata($metadata, $configuration);
    }

    public function processMetadata(ClassMetadataInfo $metadata, Configuration $configuration)
    {
        $this->convertToEntityIfNeeded($metadata, $configuration);

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata, $configuration);
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function convertToEntityIfNeeded(ClassMetadataInfo $metadata, Configuration $configuration)
    {
        if (false === $metadata->isMappedSuperclass) {
            return;
        }
        //if resource is declared, make it an entity
        if (null !== $this->resourceRegistry->findByModelClass($metadata->getName())) {
            $metadata->isMappedSuperclass = false;
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param Configuration     $configuration
     */
    private function setAssociationMappings(ClassMetadataInfo $metadata, Configuration $configuration)
    {
        $className = $metadata->getName();

        if (!class_exists($className)) {
            return;
        }

        foreach (class_parents($className) as $parent) {
            $allClassNames = $configuration->getMetadataDriverImpl()->getAllClassNames();

            //$parent must be managed by doctrine
            if (false === in_array($parent, $allClassNames, true)) {
                continue;
            }

            //load parent metadata
            $parentMetadata = new ClassMetadata($parent, $configuration->getNamingStrategy());
            $parentMetadata->wakeupReflection($this->getReflectionService());
            $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);

            //$parentMetadata must be declared as resource
            if (false === $this->isResource($parentMetadata)) {
                continue;
            }

            //apply $parentMetadata associations in current $metadata
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

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return bool
     */
    private function isResource(ClassMetadataInfo $metadata): bool
    {
        $reflClass = $metadata->getReflectionClass();
        if (!$reflClass) {
            return false;
        }

        return $reflClass->implementsInterface(ResourceInterface::class);
    }

    /**
     * @return ReflectionService [description]
     */
    private function getReflectionService(): ReflectionService
    {
        if (null === $this->reflectionService) {
            $this->reflectionService = new RuntimeReflectionService();
        }

        return $this->reflectionService;
    }
}
