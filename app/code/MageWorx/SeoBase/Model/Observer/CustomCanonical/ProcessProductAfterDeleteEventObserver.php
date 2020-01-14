<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Observer\CustomCanonical;

use Magento\Framework\Event\Observer;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use MageWorx\SeoBase\Api\CustomCanonicalRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;

class ProcessProductAfterDeleteEventObserver implements ObserverInterface
{
    /**
     * @var CustomCanonicalRepositoryInterface
     */
    private $customCanonicalRepository;

    /**
     * ProcessProductAfterDeleteEventObserver constructor.
     *
     * @param CustomCanonicalRepositoryInterface $customCanonicalRepository
     */
    public function __construct(CustomCanonicalRepositoryInterface $customCanonicalRepository)
    {
        $this->customCanonicalRepository = $customCanonicalRepository;
    }

    /**
     * Cleanup custom canonicals after product delete
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();

        if ($eventProduct && $eventProduct->getId()) {
            $this->customCanonicalRepository->deleteCustomCanonicalsByEntity(
                Rewrite::ENTITY_TYPE_PRODUCT,
                $eventProduct->getId()
            );
        }
    }
}
