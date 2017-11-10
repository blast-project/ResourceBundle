<?php

/*
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU GPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Sonata\Admin;

use Sonata\AdminBundle\Admin\AdminHelper as SonataAdminHelper;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Util\ClassUtils;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;

/**
 * Description of AdminHelper.
 *
 * @author glenn
 */
class AdminHelper extends SonataAdminHelper
{
    public function addNewInstance($object,
            FieldDescriptionInterface $fieldDescription)
    {
        return;

        $instance = $fieldDescription->getAssociationAdmin()->getNewInstance();
        $mapping = $fieldDescription->getAssociationMapping();
        $method = sprintf('add%s', Inflector::classify($mapping['fieldName']));
        if (!method_exists($object, $method)) {
            $method = rtrim($method, 's');
            if (!method_exists($object, $method)) {
                $method = sprintf('add%s',
                        Inflector::classify(Inflector::singularize($mapping['fieldName'])));
                if (!method_exists($object, $method)) {
                    throw new \RuntimeException(
                            sprintf('Please add a method %s in the %s class!',
                                    $method, ClassUtils::getClass($object))
                    );
                }
            }
        }
        $object->$method($instance);
    }
}
