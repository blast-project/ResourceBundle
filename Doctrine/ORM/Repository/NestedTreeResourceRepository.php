<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Doctrine\ORM\Repository;

use Blast\Component\Resource\Repository\ResourceRepositoryInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use InvalidArgumentException;

/**
 * Description of ResourceRepository.
 *
 * @author glenn
 */
class NestedTreeResourceRepository extends NestedTreeRepository implements ResourceRepositoryInterface
{
    public function get($id)
    {
        $resource = $this->find($id);
        if (null == $resource) {
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
        $this->_em->flush($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function update($resource): void
    {
        $this->_em->flush($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($resource): void
    {
        if (null !== $this->find($resource->getId())) {
            $this->_em->remove($resource);
            $this->_em->flush($resource);
        }
    }
}
