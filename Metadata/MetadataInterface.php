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

/**
 * @author glenn
 */
interface MetadataInterface
{
    /**
     * @return string
     */
    public function getAlias();

    /**
     * @param string $name
     *
     * @return string|array
     *
     * @throws \InvalidArgumentException
     */
    public function getParameter(string $name);

    /**
     * Return all the metadata parameters.
     *
     * @return array
     */
    public function getParameters();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter(string $name);

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getClass(string $name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasClass(string $name);
}
