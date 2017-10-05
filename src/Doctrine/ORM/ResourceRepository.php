<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blast\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;
use Blast\Bundle\ResourceBundle\Repository\ResourceRepositoryInterface;

/**
 * Description of ResourceRepository
 *
 * @author glenn
 */
class ResourceRepository extends EntityRepository implements ResourceRepositoryInterface
{

    public function get($id)
    {
        $resource = $this->find($id);
        if ( null == $resource ) {
            throw new InvalidArgumentException('Resource with the given id does not exist');
        }
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function add($resource): void
    {
        $this->_em->persist($resource);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($resource): void
    {
        if ( null !== $this->find($resource->getId()) ) {
            $this->_em->remove($resource);
            $this->_em->flush();
        }
    }

}
