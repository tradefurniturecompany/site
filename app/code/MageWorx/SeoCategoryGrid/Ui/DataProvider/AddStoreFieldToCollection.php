<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCategoryGrid\Ui\DataProvider;

use Magento\Framework\Data\Collection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

class AddStoreFieldToCollection implements AddFilterToCollectionInterface
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AddStoreFieldToCollection constructor.
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        if (isset($condition['eq']) && $condition['eq']) {

            $storeId = $condition['eq'];
            $rootId  = $this->storeManager->getStore($storeId)->getRootCategoryId();

            /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
            $collection
                ->setStore($this->storeManager->getStore($storeId))
                ->addFieldToFilter('path', ['like' => "1/$rootId/%"]);
        }
    }
}
