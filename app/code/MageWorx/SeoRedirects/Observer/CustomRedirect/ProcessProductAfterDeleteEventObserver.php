<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Observer\CustomRedirect;

use Magento\Framework\Event\ObserverInterface;

class ProcessProductAfterDeleteEventObserver implements ObserverInterface
{
    /**
     * Custom redirect resource model
     *
     * @var \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect
     */
    protected $resourceCustomRedirect;

    /**
     * @var \MageWorx\SeoRedirects\Helper\CustomRedirect\Data
     */
    protected $helperData;

    /**
     * ProcessProductAfterDeleteEventObserver constructor.
     *
     * @param \MageWorx\SeoRedirects\Helper\CustomRedirect\Data $helperData
     * @param \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect $resourceCustomRedirect
     */
    public function __construct(
        \MageWorx\SeoRedirects\Helper\CustomRedirect\Data $helperData,
        \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect $resourceCustomRedirect
    ) {
        $this->helperData             = $helperData;
        $this->resourceCustomRedirect = $resourceCustomRedirect;
    }

    /**
     * Cleanup product custom redirects after product delete
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helperData->isKeepUrlsForDeletedEntities()) {
            $eventProduct = $observer->getEvent()->getProduct();

            if ($eventProduct && $eventProduct->getId()) {
                $this->resourceCustomRedirect->deleteRedirectsByProductId($eventProduct->getId());
            }
        }

        return $this;
    }
}
