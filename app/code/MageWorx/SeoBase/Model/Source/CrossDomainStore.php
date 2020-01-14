<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Used in creating options for Add|Crop config value selection
 *
 */
namespace MageWorx\SeoBase\Model\Source;

use MageWorx\SeoBase\Helper\StoreUrl;

class CrossDomainStore extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \MageWorx\SeoBase\Helper\StoreUrl
     */
    protected $helperStoreUrl;

    /**
     * @var array
     */
    protected $options;


    /**
     * CrossDomainStore constructor
     *
     * @param StoreUrl $helperStoreUrl
     */
    public function __construct(
        StoreUrl $helperStoreUrl
    ) {
        $this->helperStoreUrl = $helperStoreUrl;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

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
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        $stores = $this->helperStoreUrl->getActiveStores();

        $this->options[] = ['value' => '', 'label' => 'Default Store URL'];
        foreach ($stores as $store) {
            /* @var $store Mage_Core_Model_Store */
            $this->options[] = [
                    'value' => $store->getId(),
                    'label' => $store->getName() . ' — ' . $store->getBaseUrl()
                ];
        }

        $this->options[] = [
            'value' => '-1',
            'label' => __('Custom URL')
        ];

        return $this->options;
    }
}
