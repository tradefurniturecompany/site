<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Observer;

class CategoryReportUpdateObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoReports\Model\Trigger\CategoryReportTrigger
     */
    protected $categoryReportTrigger;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * CategoryReportUpdateObserver constructor.
     *
     * @param \MageWorx\SeoReports\Model\Trigger\CategoryReportTrigger $categoryReportTrigger
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \MageWorx\SeoReports\Model\Trigger\CategoryReportTrigger $categoryReportTrigger,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->categoryReportTrigger = $categoryReportTrigger;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getEvent()->getCategory();

        if (!$category) {
            // magento 2.1 typo: on category event "catalog_category_delete_after_done" was used the 'product' identifier
            $category = $observer->getEvent()->getProduct();
        }

        if ($category instanceof \Magento\Catalog\Model\Category) {

            try {
                if ((int)$category->getStoreId() == 0) {
                    if ($category->isObjectNew()) {
                        $this->categoryReportTrigger->generateReportForNewCategory($category);
                    } else {
                        $this->categoryReportTrigger->generateReportForCategoryOnAllStores($category);
                    }
                } elseif ($category->isDeleted()) {
                    $this->categoryReportTrigger->regenerateReportForRemovalCategory($category);
                } elseif ((int)$category->getStoreId()) {
                    $this->categoryReportTrigger->generateReportForCategoryOnSpecificStore($category);
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return;
    }
}
