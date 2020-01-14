<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer\XmlSitemap;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductUrlGenerationObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\XmlSitemapModifier
     */
    protected $xmlSitemapModifier;

    /**
     * ProductUrlGenerationObserver constructor.
     *
     * @param \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\XmlSitemapModifier $xmlSitemapModifier
     */
    public function __construct(
        \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\XmlSitemapModifier $xmlSitemapModifier
    ) {
        $this->xmlSitemapModifier = $xmlSitemapModifier;
    }

    /**
     * Cleanup custom canonicals after category delete
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\DB\Select $select */
        $select  = $observer->getEvent()->getSelect();
        $storeId = $observer->getEvent()->getStoreId();

        if (!$select || !$storeId) {
            return;
        }

        $this->xmlSitemapModifier->modify($select, $storeId);
    }

}
