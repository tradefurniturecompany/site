<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 * 
 */

namespace Customweb\RealexCw\Observer;

class GridSyncRemoveObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Entity grid model.
     *
     * @var \Magento\Sales\Model\ResourceModel\GridInterface
     */
    protected $entityGrid;

    /**
     * @param \Magento\Sales\Model\ResourceModel\GridInterface $entityGrid
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\GridInterface $entityGrid
    ) {
        $this->entityGrid = $entityGrid;
    }

    /**
     * Handles synchronous removing of the entity from
     * corresponding grid on certain events.
     *
     * Used in the next events:
     *
     *  - customweb_realexcw_transaction_delete_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->entityGrid->purge($observer->getDataObject()->getId());
    }
}
