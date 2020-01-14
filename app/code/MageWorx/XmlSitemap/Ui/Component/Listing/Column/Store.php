<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Ui\Component\Listing\Column;

use Magento\Store\Ui\Component\Listing\Column\Store as UiStore;

/**
 * Class Store
 */
class Store extends UiStore
{
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        $origStores = $item['store_id'];

        if (is_array($origStores) && empty($origStores)) {
            return '';
        }

        if (!is_array($origStores) && $origStores === '') {
            return '';
        }

        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }

        if (in_array('0', $origStores) && count($origStores) == 1) {
            return __('For each store');
        }

        $data = $this->systemStore->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $content .= $website['label'] . "<br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . $this->escaper->escapeHtml($group['label']) . "<br/>";
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . "<br/>";
                }
            }
        }

        return $content;
    }
}
