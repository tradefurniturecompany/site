<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Rates;

use Magento\Backend\App\Action;

/**
 * Delete Rate of Method Action
 */
class Delete extends Action
{
    /**
     * @var \Amasty\ShippingTableRates\Model\RateFactory
     */
    private $rateFactory;

    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Rate
     */
    private $rateResource;

    public function __construct(
        Action\Context $context,
        \Amasty\ShippingTableRates\Model\RateFactory $rateFactory,
        \Amasty\ShippingTableRates\Model\ResourceModel\Rate $rateResource
    ) {
        parent::__construct($context);
        $this->rateFactory = $rateFactory;
        $this->rateResource = $rateResource;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('Unable to find a rate to delete'));
            $this->_redirect('amstrates/methods/index');
            return;
        }

        try {
            /**
             * @var \Amasty\ShippingTableRates\Model\Rate $rate
             */
            $rate = $this->rateFactory->create();
            $this->rateResource->load($rate, $id);
            $methodId = $rate->getMethodId();
            $this->rateResource->delete($rate);

            $this->messageManager->addSuccessMessage(__('Rate has been deleted'));
            $this->_redirect(
                'amstrates/methods/edit',
                [
                    'id' => $methodId,
                    'tab' => 'rates_section'
                ]
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('amstrates/methods/index');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShippingTableRates::amstrates');
    }
}
