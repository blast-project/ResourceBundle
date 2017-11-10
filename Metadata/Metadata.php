<?php

/*
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU GPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Metadata;

/**
 * Description of Metadata.
 *
 * @author glenn
 */
class Metadata implements MetadataInterface
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param string $name
     * @param string $applicationName
     * @param array  $parameters
     */
    private function __construct(string $alias, array $parameters)
    {
        $this->alias = $alias;
        $this->parameters = $parameters;
    }

    /**
     * @param string $alias
     * @param array  $parameters
     *
     * @return self
     */
    public static function fromAliasAndParameters(string $alias, array $parameters)
    {
        return new self($alias, $parameters);
    }

    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $name)
    {
        if (!$this->hasParameter($name)) {
            throw new \InvalidArgumentException(sprintf('Parameter "%s" is not configured for resource "%s".', $name, $this->getAlias()));
        }

        return $this->parameters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter(string $name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(string $name)
    {
        if (!$this->hasClass($name)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not configured for resource "%s".', $name, $this->getAlias()));
        }

        return $this->parameters['classes'][$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasClass(string $name)
    {
        return isset($this->parameters['classes'][$name]);
    }
}
