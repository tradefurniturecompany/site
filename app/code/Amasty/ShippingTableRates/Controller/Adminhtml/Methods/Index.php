<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Methods;

/**
 * Grid Shipping Method Action
 */
class Index extends \Amasty\ShippingTableRates\Controller\Adminhtml\Methods
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $pageResult */
        $pageResult = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $pageResult->getLayout();
        $pageResult->setActiveMenu('Amasty_ShippingTableRates::amstrates');
        $pageResult->addBreadcrumb(__('Shipping Table Rates'), __('Shipping Table Rates'));
        $pageResult->getConfig()->getTitle()->prepend(__('Methods '));

        return $pageResult;
    }
}
