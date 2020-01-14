<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product\CollectionFactory;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\SeoXTemplates\Model\DbWriterProductFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Observer class for product template apply proccess
 */
class ApplyTemplateProduct implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var DbWriterProductFactory
     */
    protected $dbWriterProductFactory;

    /**
     *
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperStore
     */
    protected $helperStore;

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
     * @param DateTime $date
     * @param DbWriterProductFactory $dbWriterProductFactory
     * @param HelperData $helperData
     * @param CollectionFactory $templateProductCollectionFactory
     * @param HelperStore $helperStore
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        DateTime $date,
        DbWriterProductFactory $dbWriterProductFactory,
        HelperData $helperData,
        CollectionFactory $templateProductCollectionFactory,
        HelperStore $helperStore,
        StoreManagerInterface $storeManager
    ) {
    
        $this->date = $date;
        $this->dbWriterProductFactory = $dbWriterProductFactory;
        $this->helperData = $helperData;
        $this->templateProductCollectionFactory = $templateProductCollectionFactory;
        $this->helperStore = $helperStore;
        $this->storeManager = $storeManager;
    }

    /**
     * Apply product template
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $this->templateProductCollectionFactory->create();

        if ($observer->getData('templateIds')) {
            $collection->addStoreModeFilter($this->storeManager->isSingleStoreMode());
            $collection->loadByIds($observer->getData('templateIds'));
        } elseif ($observer->getData('templateTypeId')) {
            $collection->addStoreModeFilter($this->storeManager->isSingleStoreMode());
            $collection->addTypeFilter($observer->getData('templateTypeId'));
            $collection->addCronFilter();
        }

        foreach ($collection as $template) {

            $template->setDateApplyStart($this->date->gmtDate());
            $template->loadItems();

            if ($template->getStoreId() == 0 && !$template->getIsSingleStoreMode()) {
                $storeIds = array_keys($this->helperStore->getActiveStores());
                foreach ($storeIds as $storeId) {
                    $this->writeTemplateForStore($template, $storeId);
                }
            } else {
                $this->writeTemplateForStore($template);
            }

            $template->setDateApplyFinish($this->date->gmtDate());
            $template->save();
        }
    }

    /**
     *
     * @param \MageWorx\SeoXTemplates\Model\Template\Product $template
     * @param int|null $nestedStoreId
     */
    protected function writeTemplateForStore($template, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();
        $dbWriter  = $this->dbWriterProductFactory->create($template->getTypeId());

        $productCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);

        while (is_object($productCollection) && $productCollection->count() > 0) {
            $dbWriter->write($productCollection, $template, $nestedStoreId);
            $from += $limit;
            $productCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }
    }
}
