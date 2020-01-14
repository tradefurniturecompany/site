<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Controller\Adminhtml\Rule;

class Duplicate extends \Amasty\Payrestriction\Controller\Adminhtml\Rule
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $this->messageManager->addError(__('Please select a rule to duplicate.'));
            return $this->_redirect('*/*');
        }

        try {
            $model  = $this->_objectManager->create('Amasty\Payrestriction\Model\Rule')->load($id);
            if (!$model->getId()){
                $this->messageManager->addError(__('Please select a rule to duplicate.'));
                return $this->_redirect('*/*');
            }

            $rule = clone $model;
            $rule->setIsActive(0);
            $rule->setId(null);
            $rule->save();

            $this->messageManager->addSuccess(
                __('The rule has been duplicated. Please feel free to activate it.')
            );
            return $this->_redirect('*/*/edit', array('id' => $rule->getId()));
        }
        catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->_redirect('*/*');
        }

        //unreachable
        return $this->_redirect('*/*');

    }
}
