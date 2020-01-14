<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Rates;

/**
 * Edit Rate of Method Action
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Amasty\ShippingTableRates\Model\RateFactory
     */
    private $rateFactory;

    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Rate
     */
    private $rateResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\ShippingTableRates\Model\RateFactory $rateFactory,
        \Amasty\ShippingTableRates\Model\ResourceModel\Rate $rateResource
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->rateFactory = $rateFactory;
        $this->rateResource = $rateResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /**
         * @var \Amasty\ShippingTableRates\Model\Rate $objectRate
         */
        $objectRate = $this->rateFactory->create();
        $this->rateResource->load($objectRate, $id);
        $methodId = $this->getRequest()->getParam('method_id');

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $objectRate->addData($data);
        }

        if ($methodId && !$objectRate->getId()) {
            $objectRate->setMethodId($methodId);
            $objectRate->setWeightFrom('0');
            $objectRate->setQtyFrom('0');
            $objectRate->setPriceFrom('0');
            $objectRate->setWeightTo(\Amasty\ShippingTableRates\Model\Rate::MAX_VALUE);
            $objectRate->setQtyTo(\Amasty\ShippingTableRates\Model\Rate::MAX_VALUE);
            $objectRate->setPriceTo(\Amasty\ShippingTableRates\Model\Rate::MAX_VALUE);
        }

        $this->coreRegistry->register('amtable_rate', $objectRate);

        $this->_view->loadLayout();

        $this->_setActiveMenu('Amasty_ShippingTableRates::amstrates')->_addBreadcrumb(__('Table Rates'), __('Table Rates'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend('Rate Configuration');

        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShippingTableRates::amstrates');
    }
}
