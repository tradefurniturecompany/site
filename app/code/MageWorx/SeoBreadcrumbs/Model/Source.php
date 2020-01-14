<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBreadcrumbs\Model;

use Magento\Framework\Option\ArrayInterface;

abstract class Source implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    abstract public function toOptionArray();

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $_tmpOptions = $this->toOptionArray();
        $_options = [];
        foreach ($_tmpOptions as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}
