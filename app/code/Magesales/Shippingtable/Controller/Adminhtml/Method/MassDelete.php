<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Method;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Method;

class MassDelete extends Method
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
		
		$ids = $this->getRequest()->getParam('methods');
        if (!is_array($ids)) {
             $this->messageManager->addError(__('Please select records'));
             return $resultRedirect->setPath('*/*/');
             return;
        }

        $deleted = 0;
        foreach ($ids as $id)
		{
            $this->_methodFactory->create()->load($id)->delete();
            $deleted++;
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );

        return $resultRedirect->setPath('*/*/');
    }
}
