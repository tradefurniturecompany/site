<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Ui\Component\Listing\Column\TargetStore;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;
use MageWorx\SeoBase\Model\Source\CustomCanonical\TargetStoreId;

class Options extends StoreOptions
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions['Same as Source Entity']['label'] = __('Same as Source Entity');
        $this->currentOptions['Same as Source Entity']['value'] = TargetStoreId::SAME_AS_SOURCE_ENTITY;

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
