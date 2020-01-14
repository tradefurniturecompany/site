<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Rate;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Rate;

class Save extends Rate
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        $mid    = $this->getRequest()->getParam('mid');
	    $model  = $this->_rateFactory->create()->load($id);
		$this->_backendSession = \Magento\Framework\App\ObjectManager::getInstance()
    ->get('Magento\Backend\Model\Session');
		$data = $this->getRequest()->getPostValue();
		
		if (!$data) {
            $this->messageManager->addError(__('Unable to find a rate to save'));
            return $resultRedirect->setPath('shippingtable/method/index');
        }
		
		try {

            $methodId = $model->getMethodId();
            if (!$methodId)
            {
                $methodId = $data['method_id'];    
            }		
		    $model->setData($data)->setId($id);
			$model->save();
			
			$this->_backendSession->setFormData(false);
			
			$msg = __('Rate has been successfully saved');
            $this->messageManager->addSuccess($msg);
            return $resultRedirect->setPath('shippingtable/method/edit', ['id'=> $methodId, 'tab'=>'rates']);
			
        } 
        catch (\Exception $e) {
            $this->messageManager->addError('This rate already exist!');
            $this->_backendSession->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id, 'mid'=> $methodId]);
        }	
	}
}
