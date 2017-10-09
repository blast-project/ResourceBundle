<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blast\Bundle\ResourceBundle\Sonata\Admin;

use Blast\CoreBundle\Admin\CoreAdmin as BlastAdmin;
use Sonata\AdminBundle\Admin\AbstractAdmin as SonataAdmin;
use Blast\Bundle\ResourceBundle\Repository\ResourceRepositoryInterface;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;

/**
 * Description of ResourceAdmin
 *
 * @author glenn
 */
class ResourceAdmin extends BlastAdmin
{

    public function configure()
    {
        SonataAdmin::configure();
        $this->getLabelTranslatorStrategy()
                ->setPrefix($this->getLabel());
    }

    /**
     *
     * @var ResourceRepositoryInterface 
     */
    protected $resourceRepository;

    public function getResourceRepository(): ResourceRepositoryInterface
    {
        return $this->resourceRepository;
    }

    public function setResourceRepository(ResourceRepositoryInterface $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create($object)
    {
        $this->prePersist($object);
        foreach ( $this->extensions as $extension ) {
            $extension->prePersist($this, $object);
        }

        $result = $this->getResourceRepository()->add($object);
        // BC compatibility
        if ( null !== $result ) {
            $object = $result;
        }

        $this->postPersist($object);
        foreach ( $this->extensions as $extension ) {
            $extension->postPersist($this, $object);
        }

        $this->createObjectSecurity($object);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function getObject($id)
    {
        $object = $this->getResourceRepository()->get($id);
        foreach ( $this->getExtensions() as $extension ) {
            $extension->alterObject($this, $object);
        }

        return $object;
    }

}
