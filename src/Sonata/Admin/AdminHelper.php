<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blast\Bundle\ResourceBundle\Sonata\Admin;

use Sonata\AdminBundle\Admin\AdminHelper as SonataAdminHelper;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Util\ClassUtils;
use Sonata\AdminBundle\Exception\NoValueException;
use Sonata\AdminBundle\Util\FormBuilderIterator;
use Sonata\AdminBundle\Util\FormViewIterator;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;

/**
 * Description of AdminHelper
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
        if ( !method_exists($object, $method) ) {
            $method = rtrim($method, 's');
            if ( !method_exists($object, $method) ) {
                $method = sprintf('add%s',
                        Inflector::classify(Inflector::singularize($mapping['fieldName'])));
                if ( !method_exists($object, $method) ) {
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
