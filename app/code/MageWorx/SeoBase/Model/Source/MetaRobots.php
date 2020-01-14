<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source;

class MetaRobots extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

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

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            ['value' => '', 'label' => __('Use Config')],
            ['value' => 'INDEX, FOLLOW', 'label' => 'INDEX, FOLLOW'],
            ['value' => 'INDEX, NOFOLLOW', 'label' => 'INDEX, NOFOLLOW'],
            ['value' => 'NOINDEX, FOLLOW', 'label' => 'NOINDEX, FOLLOW'],
            ['value' => 'NOINDEX, NOFOLLOW', 'label' => 'NOINDEX, NOFOLLOW'],
            ['value' => 'INDEX, FOLLOW, NOARCHIVE', 'label' => 'INDEX, FOLLOW, NOARCHIVE'],
            ['value' => 'INDEX, NOFOLLOW, NOARCHIVE', 'label' => 'INDEX, NOFOLLOW, NOARCHIVE'],
            ['value' => 'NOINDEX, NOFOLLOW, NOARCHIVE', 'label' => 'NOINDEX, NOFOLLOW, NOARCHIVE'],
        ];
    }
}
