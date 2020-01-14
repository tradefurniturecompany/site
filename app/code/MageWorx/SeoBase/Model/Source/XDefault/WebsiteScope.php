<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\Source\XDefault;

class WebsiteScope implements \Magento\Framework\Option\ArrayInterface
{
    protected $options;

    /**
     *
     * @var \MageWorx\SeoBase\Helper\Hreflangs
     */
    protected $helperHreflangs;

    public function __construct(\MageWorx\SeoBase\Helper\Hreflangs $helperHreflangs)
    {
        $this->helperHreflangs = $helperHreflangs;
    }

    public function toOptionArray()
    {
        $stores = $this->helperHreflangs->getAllEnabledStoreByType(null);
        $values = [];

        foreach ($stores as $store) {
            $sortOrder        = $store->getSortOrder();
            $websiteId        = $store->getWebsite()->getId();
            $websiteName      = $store->getWebsite()->getName();
            $websiteSortOrder = $store->getWebsite()->getSortOrder();
            $storeName        = $store->getName();
            $storeCode        = $store->getCode();
            $storeId          = $store->getStoreId();

            $value = $websiteName . " | " . $storeName . " (code: " . $storeCode . " | ID: " . $storeId . ")";

            $values[] = [
                'label'              => $value,
                'value'              => $storeId,
                'website_id'         => $websiteId,
                'website_sort_order' => $websiteSortOrder,
                'store_sort_order'   => $sortOrder
            ];
        }
        usort($values, [$this, "cmp"]);

        return $values;
    }

    protected function cmp($a, $b)
    {
        $orderBy = ['website_id' => 'asc', 'value' => 'asc'];
        $result = 0;
        foreach ($orderBy as $key => $value) {
            if ($a[$key] == $b[$key]) {
                continue;
            }
            $result = ($a[$key] < $b[$key]) ? -1 : 1;
            if ($value == 'desc') {
                $result = -$result;
                break;
            }
        }
        return $result;
    }
}
