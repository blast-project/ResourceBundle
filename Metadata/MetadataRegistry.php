<?php

/*
 * This file is part of the Sil Project.
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU GPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/*
 * This file is part of the Blast Project package.
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Metadata;

class MetadataRegistry implements MetadataRegistryInterface
{
    /**
     * @var array|MetadataInterface[]
     */
    private $metadata = [];

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $alias)
    {
        if (!array_key_exists($alias, $this->metadata)) {
            throw new \InvalidArgumentException(sprintf('Resource "%s" does not exist.', $alias));
        }

        return $this->metadata[$alias];
    }

    /**
     * {@inheritdoc}
     */
    public function getByClass(string $className)
    {
        foreach ($this->metadata as $metadata) {
            if ($className === $metadata->getClass('entity')) {
                return $metadata;
            }
        }
        throw new \InvalidArgumentException(sprintf('Resource with entity class "%s" does not exist.', $className));
    }

    /**
     * {@inheritdoc}
     */
    public function add(MetadataInterface $metadata)
    {
        $this->metadata[$metadata->getAlias()] = $metadata;
    }

    public function addFromAliasAndParameters(string $alias, array $parameters)
    {
        $this->add(Metadata::fromAliasAndParameters($alias, $parameters));
    }
}
