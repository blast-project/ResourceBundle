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
use Blast\Component\Resource\Metadata\MetadataRegistryInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 */
class RepositorySubscriber implements EventSubscriber
{
    /**
     * @var MetadataRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
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
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $this->processRepositoryClass($eventArgs->getClassMetadata());
    }

    /**
     * @param ClassMetadata $metadata
     */
    public function processRepositoryClass(ClassMetadata $metadata): void
    {
        $resourceMetadata = $this->resourceRegistry->findByModelClass($metadata->getName());
        if (null === $resourceMetadata) {
            return;
        }
        if ($resourceMetadata->getClassMap()->hasRepository()) {
            $metadata->setCustomRepositoryClass($resourceMetadata->getClassMap()->getRepository());
        }
    }
}
