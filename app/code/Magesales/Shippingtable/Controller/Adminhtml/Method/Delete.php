<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Method;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Method;

class Delete extends Method
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = $this->_methodFactory->create()->load($id);

		if ($id && !$model->getId()) {
    		$this->messageManager->addError($this->__('Record does not exist'));
			return $resultRedirect->setPath('*/*/');
		}
         
        try {
            $model->delete();
            $this->messageManager->addSuccess(__('Shipping method has been successfully deleted'));
        } 
        catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        
        return $resultRedirect->setPath('*/*/');
	}
}
