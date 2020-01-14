<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Observer;

class ProductReportUpdateObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoReports\Model\Trigger\ProductReportTrigger
     */
    protected $productReportTrigger;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * ProductReportUpdateObserver constructor.
     *
     * @param \MageWorx\SeoReports\Model\Trigger\ProductReportTrigger $productReportTrigger
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \MageWorx\SeoReports\Model\Trigger\ProductReportTrigger $productReportTrigger,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->productReportTrigger = $productReportTrigger;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();

        if ($product instanceof \Magento\Catalog\Model\Product) {

            if ((int)$product->getStoreId() == 0) {
                if ($product->isObjectNew()) {
                    $this->productReportTrigger->generateReportForNewProduct($product);
                } else {
                    $this->productReportTrigger->generateReportForProductOnAllStores($product);
                }
            } elseif ($product->isDeleted()) {
                $this->productReportTrigger->regenerateReportForRemovalProduct($product);
            } elseif ((int)$product->getStoreId()) {
                $this->productReportTrigger->generateReportForProductOnSpecificStore($product);
            }
        }

        return;
    }
}
