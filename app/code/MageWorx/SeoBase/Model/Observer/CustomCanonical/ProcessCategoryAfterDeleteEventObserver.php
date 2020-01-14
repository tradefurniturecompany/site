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

class ProcessCategoryAfterDeleteEventObserver implements ObserverInterface
{
    /**
     * @var CustomCanonicalRepositoryInterface
     */
    private $customCanonicalRepository;

    /**
     * ProcessCategoryAfterDeleteEventObserver constructor.
     *
     * @param CustomCanonicalRepositoryInterface $customCanonicalRepository
     */
    public function __construct(CustomCanonicalRepositoryInterface $customCanonicalRepository)
    {
        $this->customCanonicalRepository = $customCanonicalRepository;
    }

    /**
     * Cleanup custom canonicals after category delete
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $eventCategory = $observer->getEvent()->getCategory();

        if (empty($eventCategory)) {
            // getProduct() method retrieve category object - typo in magento 2.1
            $eventCategory = $observer->getEvent()->getProduct();
        }

        if ($eventCategory && $eventCategory->getId()) {
            $this->customCanonicalRepository->deleteCustomCanonicalsByEntity(
                Rewrite::ENTITY_TYPE_CATEGORY,
                $eventCategory->getId()
            );
        }
    }
}
