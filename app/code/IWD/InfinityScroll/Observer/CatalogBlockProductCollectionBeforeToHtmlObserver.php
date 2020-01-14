<?php

namespace IWD\InfinityScroll\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class CatalogBlockProductCollectionBeforeToHtmlObserver implements ObserverInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * CatalogBlockProductCollectionBeforeToHtmlObserver constructor.
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        $lastPageNumber = $productCollection->getLastPageNumber();
        $curPage = $productCollection->getCurPage();

        $this->registry->register('catalogCollectionCurPage', $curPage);
        $this->registry->register('catalogCollectionLastPageNumber', $lastPageNumber);

        return $this;
    }
}
