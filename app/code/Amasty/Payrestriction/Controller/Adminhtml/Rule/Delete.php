<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Controller\Adminhtml\Rule;

class Delete extends \Amasty\Payrestriction\Controller\Adminhtml\Rule
{

    public function execute()
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Amasty\Payrestriction\Model\Rule')->load($id);

        if ($id && !$model->getId()) {
            $this->messageManager->addError(__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            $this->messageManager->addSuccess(
                __('Payment Restriction has been successfully deleted'));
        }
        catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->_redirect('*/*/');

    }
}
