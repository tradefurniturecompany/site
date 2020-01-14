<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Observer\DpRedirect;

use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\DpRedirect as DpRedirectResource;
use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\DpRedirect\CollectionFactory as DpRedirectCollectionFactory;
use MageWorx\SeoRedirects\Helper\DpRedirect\Data as HelperData;
use MageWorx\SeoAll\Helper\Store as HelperStore;

class RemoveOldRedirects
{
    /**
     * @var DpRedirectCollectionFactory
     */
    protected $dpRedirectCollectionFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var DpRedirectResource
     */
    protected $dpRedirectResource;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     * RemoveOldRedirects constructor.
     *
     * @param DpRedirectResource $dpRedirectResource
     * @param DpRedirectCollectionFactory $dpRedirectCollectionFactory
     * @param HelperData $helperData
     * @param HelperStore $helperStore
     */
    public function __construct(
        DpRedirectResource $dpRedirectResource,
        DpRedirectCollectionFactory $dpRedirectCollectionFactory,
        HelperData $helperData,
        HelperStore $helperStore
    ) {
        $this->dpRedirectResource          = $dpRedirectResource;
        $this->dpRedirectCollectionFactory = $dpRedirectCollectionFactory;
        $this->helperData                  = $helperData;
        $this->helperStore                 = $helperStore;
    }

    /**
     * @throws \Exception
     */
    public function delete()
    {
        $liveInterval = $this->helperData->getCountStableDay();

        if ($liveInterval <= 0) {
            return;
        }

        $where = new \Zend_Db_Expr('`date_created` < DATE_ADD(NOW(), INTERVAL ' . -$liveInterval . ' DAY)');

        foreach ($this->helperStore->getActiveStores() as $store) {

            if ($this->helperData->isEnabled($store->getId())) {

                $redirectCollection = $this->dpRedirectCollectionFactory->create();
                $redirectCollection->addFieldToFilter('store_id', $store->getId());
                $redirectCollection->getSelect()->where($where);

                foreach ($redirectCollection as $redirect) {
                    $this->dpRedirectResource->delete($redirect);
                }
            }
        }

        return;
    }
}
