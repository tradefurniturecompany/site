<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Observer;

class PageReportUpdateObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoReports\Model\Trigger\PageReportTrigger
     */
    protected $pageReportTrigger;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * PageReportUpdateObserver constructor.
     *
     * @param \MageWorx\SeoReports\Model\Trigger\PageReportTrigger $pageReportTrigger
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \MageWorx\SeoReports\Model\Trigger\PageReportTrigger $pageReportTrigger,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->pageReportTrigger = $pageReportTrigger;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $observer->getEvent()->getObject();

        if ($page instanceof \Magento\Cms\Model\Page) {
            try {
                if ($page->isObjectNew()) {
                    $this->pageReportTrigger->generateReportForNewPage($page);
                } elseif ($page->isDeleted()) {
                    $this->pageReportTrigger->regenerateReportForRemovalPage($page);
                } else {
                    $this->pageReportTrigger->generateReportForPage($page);
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return;
    }
}
