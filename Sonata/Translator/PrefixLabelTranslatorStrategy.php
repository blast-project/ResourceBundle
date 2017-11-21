<?php

/*
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Sonata\Translator;

use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;

/**
 * @author glenn
 */
class PrefixLabelTranslatorStrategy implements LabelTranslatorStrategyInterface
{
    /**
     * @var string
     */
    protected $prefix;

    public function getLabel($label, $context = '', $type = ''): string
    {
        $this->handleLabelForContext($label, $context);

        $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $label));
        $translationKey = $this->prefix . '.' . $context . '.' . $type . '.' . $key;

        return $translationKey;
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    private function handleLabelForContext(&$label, $context)
    {
        // Stripping first label elements to get only « list » or « create » label under breadcrumb labels.
        // Also acts as a underscore ltrim for label like « _actions » or « _options »
        if ($context === 'breadcrumb' || strpos($label, '_') === 0) {
            $label = explode('_', $label);
            $label = end($label);
        }
    }
}
