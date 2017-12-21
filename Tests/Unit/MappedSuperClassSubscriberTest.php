<?php

/*
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Blast\Bundle\ResourceBundle\Tests\Unit\Entity\MyEntity;
use Blast\Bundle\ResourceBundle\Tests\Unit\Entity\MyParentEntity;
use Blast\Component\Resource\Metadata\MetadataRegistry;
use Blast\Bundle\ResourceBundle\Doctrine\ORM\EventListener\MappedSuperClassSubscriber;

/**
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 */
class MappedSuperClassSubscriberTest extends TestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        $paths = array(__DIR__ . '/../Resources/doctrine/');
        $isDevMode = true;

        // the connection configuration
        $dbParams = array(
          'driver'   => 'pdo_mysql',
          'user'     => 'root',
          'password' => '',
          'dbname'   => 'foo',
        );

        $config = Setup::createYAMLMetadataConfiguration($paths, $isDevMode);
        $this->entityManager = EntityManager::create($dbParams, $config);
    }

    public function test_parent_entity_should_stay_a_mappedsuperclass()
    {
        $registry = new MetadataRegistry();
        $mappedSuperClassSubscriber = new MappedSuperClassSubscriber($registry);
        $registry->addFromAliasAndParameters('app.my_entity', ['classes'=>['model'=>MyEntity::class]]);

        $myEntityMetadata = $this->entityManager->getClassMetadata(MyEntity::class);
        $myParentEntityMetadata = $this->entityManager->getClassMetadata(MyParentEntity::class);
        $emConfig = $this->entityManager->getConfiguration();

        $this->assertTrue($myEntityMetadata->isMappedSuperclass);
        $mappedSuperClassSubscriber->processMetadata($myEntityMetadata, $emConfig);
        $this->assertFalse($myEntityMetadata->isMappedSuperclass);

        $this->assertTrue($myParentEntityMetadata->isMappedSuperclass);
        $mappedSuperClassSubscriber->processMetadata($myParentEntityMetadata, $emConfig);
        $this->assertTrue($myParentEntityMetadata->isMappedSuperclass);
    }
}
