<?php

/*
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Doctrine\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Blast\Bundle\ResourceBundle\Repository\ResourceRepositoryInterface;
use InvalidArgumentException;

/**
 * Description of ResourceRepository.
 *
 * @author glenn
 */
class ResourceRepository extends EntityRepository implements ResourceRepositoryInterface
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
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($resource): void
    {
        if (null !== $this->find($resource->getId())) {
            $this->_em->remove($resource);
            $this->_em->flush();
        }
    }
}
