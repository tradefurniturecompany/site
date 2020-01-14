<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Methods;

/**
 * Delete Shipping Method Action
 */
class Delete extends \Amasty\ShippingTableRates\Controller\Adminhtml\Methods
{
    /**
     * @var \Amasty\ShippingTableRates\Model\MethodFactory
     */
    private $methodFactory;

    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Method
     */
    private $methodResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\ShippingTableRates\Model\MethodFactory $methodFactory,
        \Amasty\ShippingTableRates\Model\ResourceModel\Method $methodResource
    ) {
        parent::__construct($context);
        $this->methodFactory = $methodFactory;
        $this->methodResource = $methodResource;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /**
         * @var \Amasty\ShippingTableRates\Model\Method $model
         */
        $model = $this->methodFactory->create();
        $this->methodResource->load($model, $id);

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $this->methodResource->delete($model);
            $this->messageManager->addSuccessMessage(
                __('Shipping method has been successfully deleted')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShippingTableRates::amstrates');
    }
}
