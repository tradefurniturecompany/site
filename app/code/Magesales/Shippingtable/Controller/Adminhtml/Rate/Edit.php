<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Rate;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Rate;

class Edit extends Rate
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        $model = $this->_rateFactory->create()->load($id);		
	
		$mid =  (int) $this->getRequest()->getParam('mid');
		
		if(!$mid)
		{
			$mid = $model->getMethodId();
		}
			
		if (!$mid && !$model->getId()) {
    		$this->messageManager->addError(__('Record #%1 does not exist', $id));
		    return $resultRedirect->setPath('shippingtable/method/index');
		}   
		$this->_backendSession = \Magento\Framework\App\ObjectManager::getInstance()
    ->get('Magento\Backend\Model\Session');
		$data = $this->_backendSession->getFormData(true);
		
		if (!empty($data))
		{
			$model->setData($data);
		}
        
        if ($mid && !$model->getId()){
            $model->setMethodId($mid);
            $model->setWeightFrom('0');
            $model->setQtyFrom('0');
            $model->setPriceFrom('0');
            $model->setWeightTo('99999999');
            $model->setQtyTo('99999999');
            $model->setPriceTo('99999999');
        }
		
		$this->registry->register('shippingtable_rate', $model);

		$this->_view->loadLayout();
		
		$this->_addContent($this->_view->getLayout()->createBlock('\Magesales\Shippingtable\Block\Adminhtml\Rate\Edit'));
        
		$this->_view->renderLayout();
	}
}
