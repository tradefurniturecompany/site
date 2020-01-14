<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Manager;

use MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Cache status manager
 */
class Product implements \MageWorx\SeoXTemplates\Model\Template\ManagerInterface
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product\CollectionFactory
     */
    protected $templateProductCollectionFactory;

    /**
     *
     * @param CollectionFactory $templateProductCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $templateProductCollectionFactory,
        StoreManagerInterface $storeManager
    ) {

        $this->templateProductCollectionFactory = $templateProductCollectionFactory;
        $this->storeManager                     = $storeManager;
    }

    /**
     * @return array
     */
    public function getAvailableIds()
    {
        $isSingleStoreMode = (int)$this->storeManager->isSingleStoreMode();

        /** @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product\Collection */
        $collection = $this->templateProductCollectionFactory->create();
        $collection->addStoreModeFilter($isSingleStoreMode);

        return $collection->getAllIds();
    }

    /**
     * @return array
     */
    public function getColumnsValues()
    {
        /** @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product\Collection */
        $collection = $this->templateProductCollectionFactory->create();

        $col = [];

        foreach ($collection->getItems() as $item) {
            $col[] = $item->getData('template_id') . ' - ' .
                $item->getData('name') . ' - ' .
                $item->getData('code') . ' - ' .
                $this->storeManager->getStore($item->getData('store_id'))->getName();
        }

        return $col;
    }
}
