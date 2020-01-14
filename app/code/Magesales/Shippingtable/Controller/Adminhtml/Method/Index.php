<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Method;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Method;

class Index extends Method
{
	public function execute()
    {
        $this->_view->loadLayout();
		$resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Sales::shipping_table');
        $resultPage->addBreadcrumb(__('Shipping Table Rates'), __('Shipping Table Rates'));
        $resultPage->addBreadcrumb(__('Manage Shipping Table Rates'), __('Shipping Table Rates'));
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Table Rates'));
		$this->_addContent($this->_view->getLayout()->createBlock('\Magesales\Shippingtable\Block\Adminhtml\Method'));
        
		$this->_view->renderLayout();
    }
}
