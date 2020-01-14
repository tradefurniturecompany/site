<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source\CustomCanonical;

use MageWorx\SeoBase\Helper\StoreUrl;

class TargetStoreId extends \MageWorx\SeoAll\Model\Source
{
    /**
     * @var int
     */
    const SAME_AS_SOURCE_ENTITY = 0;

    /**
     * @var StoreUrl
     */
    private $helperStoreUrl;

    /**
     * @var array
     */
    private $options;

    /**
     * TargetStoreId constructor.
     *
     * @param StoreUrl $helperStoreUrl
     */
    public function __construct(
        StoreUrl $helperStoreUrl
    ) {
        $this->helperStoreUrl = $helperStoreUrl;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
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
    private function getAllOptions()
    {
        $stores = $this->helperStoreUrl->getActiveStores();

        $this->options[] = ['value' => self::SAME_AS_SOURCE_ENTITY, 'label' => 'Same as Source Entity'];

        /* @var $store \Magento\Store\Model\Store */
        foreach ($stores as $store) {
            $this->options[] = [
                'value' => $store->getId(),
                'label' => $store->getName() . ' — ' . $store->getBaseUrl()
            ];
        }

        return $this->options;
    }
}
