<?php

/*
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
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

/**
 * @author glenn
 */
interface MetadataRegistryInterface
{
    /**
     * @return iterable|MetadataInterface[]
     */
    public function getAll();

    /**
     * @param string $alias
     *
     * @return MetadataInterface
     *
     * @throws \InvalidArgumentException
     */
    public function get(string $alias);

    /**
     * @param string $className
     *
     * @return MetadataInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getByClass(string $className);

    /**
     * @param MetadataInterface $metadata
     */
    public function add(MetadataInterface $metadata);

    /**
     * @param string $alias
     * @param array  $parameters
     */
    public function addFromAliasAndParameters(string $alias, array $parameters);
}
