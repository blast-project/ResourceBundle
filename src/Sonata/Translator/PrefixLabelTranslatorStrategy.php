<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blast\Bundle\ResourceBundle\Sonata\Translator;

use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * @author glenn
 */
class PrefixLabelTranslatorStrategy implements LabelTranslatorStrategyInterface
{

    /**
     *
     * @var string 
     */
    protected $prefix;

    public function getLabel($label, $context = '', $type = ''): string
    {

        $labelParts = explode('_', $label);
        $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', end($labelParts)));
        $translationKey = $this->prefix . '.' . $context . '.' . $type . '.' . $key;
        return $translationKey;
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

}
