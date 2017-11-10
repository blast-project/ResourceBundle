<?php

/*
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Doctrine\ORM\NamingStrategy;

use Doctrine\ORM\Mapping\NamingStrategy;
use ReflectionClass;

/**
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 */
class UnderscoredBundlePrefixStrategy implements NamingStrategy
{
    /**
     * @var int
     */
    private $case = CASE_LOWER;

    /**
     * @var NamingStrategy
     */
    private $fallbackStrategy;

    /**
     * @var array
     */
    private $namingMap = [];

    /**
     * UnderscoredBundleNamePrefix constructor.
     *
     * @param array $enabledBundles
     * @param array $options
     */
    public function __construct(array $enabledBundles, array $options = [])
    {
        $this->fallbackStrategy = $options['fallback'];
        $this->namingMap = $this->buildNamingMap($enabledBundles,
            $options['filter']);
    }

    /**
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        $prefix = $this->getTableNamePrefix($className);
        if (strpos($className, '\\') !== false) {
            $className = substr($className, strrpos($className, '\\') + 1);
        }

        return $prefix . $this->underscore($className);
    }

    /**
     * {@inheritdoc}
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $this->underscore($propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function embeddedFieldToColumnName($propertyName,
        $embeddedColumnName, $className = null, $embeddedClassName = null)
    {
        return $this->underscore($propertyName) . '_' . $embeddedColumnName;
    }

    /**
     * {@inheritdoc}
     */
    public function referenceColumnName()
    {
        return $this->case === CASE_UPPER ? 'ID' : 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function joinColumnName($propertyName, $className = null)
    {
        return $this->underscore($propertyName) . '_' . $this->referenceColumnName();
    }

    /**
     * {@inheritdoc}
     */
    public function joinTableName($sourceEntity, $targetEntity,
        $propertyName = null)
    {
        $rc = new ReflectionClass($targetEntity);

        return $this->classToTableName($sourceEntity) . '__' . $this->underscore($rc->getShortName());
    }

    /**
     * {@inheritdoc}
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return $this->classToTableName($entityName) . '_' .
            ($referencedColumnName ?: $this->referenceColumnName());
    }

    /**
     * @param array $enabledBundles
     * @param array $filter
     *
     * @return array
     */
    private function buildNamingMap(array $enabledBundles, array $filter)
    {
        $namingMap = [];

        foreach ($enabledBundles as $bundleName => $bundleNamespace) {
            if (count($filter) && !$this->matchFilter($bundleNamespace, $filter)) {
                continue;
            }
            $bundleNamespace = preg_replace("/$bundleName$/", '', $bundleNamespace);
            $bundleName = preg_replace('/Bundle$/', '', $bundleName);
            $namingMap[$this->underscore($bundleName)] = $bundleNamespace;
        }

        return $namingMap;
    }

    /**
     * Get prefix for table from naming map.
     *
     * @param string $className
     *
     * @return string
     */
    private function getTableNamePrefix($className)
    {
        $name = ltrim($className, '\\');
        foreach ($this->namingMap as $prefix => $namespace) {
            if (strpos($name, $namespace) === 0) {
                return $prefix . '_';
            }
        }

        return '';
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function underscore($string)
    {
        $str = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string);

        if ($this->case === CASE_UPPER) {
            return strtoupper($str);
        }

        return strtolower($str);
    }

    private function matchFilter(string $name, array $filter)
    {
        foreach ($filter as $regex) {
            if (preg_match("/.*$regex.*/", $name)) {
                return true;
            }
        }

        return false;
    }
}
