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
