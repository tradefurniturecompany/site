<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Ui\Component\Listing\Column;

/**
 * Class Store
 */
class Store extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                if (!empty($item[$this->storeKey])) {
                    $item[$this->getData('name') . '_orig'] = $item[$this->storeKey];
                }
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }
}
