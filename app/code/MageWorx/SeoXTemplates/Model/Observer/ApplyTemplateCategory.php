<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category\CollectionFactory;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\SeoXTemplates\Model\DbWriterCategoryFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Observer class for category template apply proccess
 */
class ApplyTemplateCategory implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var DbWriterCategoryFactory
     */
    protected $dbWriterCategoryFactory;

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
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category\CollectionFactory
     */
    protected $templateCategoryCollectionFactory;

    /**
     *
     * @param DateTime $date
     * @param DbWriterCategoryFactory $dbWriterCategoryFactory
     * @param HelperData $helperData
     * @param CollectionFactory $templateCategoryCollectionFactory
     * @param HelperStore $helperStore
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        DateTime $date,
        DbWriterCategoryFactory $dbWriterCategoryFactory,
        HelperData $helperData,
        CollectionFactory $templateCategoryCollectionFactory,
        HelperStore $helperStore,
        StoreManagerInterface $storeManager
    ) {
        $this->date = $date;
        $this->dbWriterCategoryFactory = $dbWriterCategoryFactory;
        $this->helperData = $helperData;
        $this->templateCategoryCollectionFactory = $templateCategoryCollectionFactory;
        $this->helperStore = $helperStore;
        $this->storeManager = $storeManager;
    }

    /**
     * Apply category template
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $this->templateCategoryCollectionFactory->create();

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
        $dbWriter  = $this->dbWriterCategoryFactory->create($template->getTypeId());

        $productCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);

        while (is_object($productCollection) && $productCollection->count() > 0) {
            $dbWriter->write($productCollection, $template, $nestedStoreId);
            $from += $limit;
            $productCollection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }
    }
}
