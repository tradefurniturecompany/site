<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Model\ResourceModel\Template\LandingPage\CollectionFactory;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\SeoXTemplates\Model\DbWriterLandingPageFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Observer class for landing page template apply proccess
 */
class ApplyTemplateLandingPage implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var DbWriterLandingPageFactory
     */
    protected $dbWriterLandingPageFactory;

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
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\LandingPage\CollectionFactory
     */
    protected $templateLandingPageCollectionFactory;

    /**
     *
     * @param DateTime $date
     * @param DbWriterLandingPageFactory $dbWriterLandingPageFactory
     * @param HelperData $helperData
     * @param CollectionFactory $templateLandingPageCollectionFactory
     * @param HelperStore $helperStore
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        DateTime $date,
        DbWriterLandingPageFactory $dbWriterLandingPageFactory,
        HelperData $helperData,
        CollectionFactory $templateLandingPageCollectionFactory,
        HelperStore $helperStore,
        StoreManagerInterface $storeManager
    ) {
        $this->date = $date;
        $this->dbWriterLandingPageFactory = $dbWriterLandingPageFactory;
        $this->helperData = $helperData;
        $this->templateLandingPageCollectionFactory = $templateLandingPageCollectionFactory;
        $this->helperStore = $helperStore;
        $this->storeManager = $storeManager;
    }

    /**
     * Apply LandingPage template
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $this->templateLandingPageCollectionFactory->create();

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

            if ($template->getStoreId() == 0
                && !$template->getIsSingleStoreMode()
                && !$template->getUseForDefaultValue()
            ) {
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
     * @param \MageWorx\SeoXTemplates\Model\Template\LandingPage $template
     * @param null $nestedStoreId
     */
    protected function writeTemplateForStore($template, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();
        $dbWriter  = $this->dbWriterLandingPageFactory->create($template->getTypeId());

        $collection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);

        while (is_object($collection) && $collection->count() > 0) {
            $dbWriter->write($collection, $template, $nestedStoreId);
            $from += $limit;
            $collection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }
    }
}
