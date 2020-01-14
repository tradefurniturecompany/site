<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Method;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Method;

class Save extends Method
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        $model = $this->_methodFactory->create();
		$data = $this->getRequest()->getPostValue();
		$this->_backendSession = \Magento\Framework\App\ObjectManager::getInstance()
    ->get('Magento\Backend\Model\Session');
		if ($data) {
		    $model->setData($data);
			$model->setId($id);
			try {
			    $this->prepareForSave($model);
			    
				$model->save();
				
				if ($model->getData('import_clear')){
				    $this->_rateFactory->create()->deleteBy($model->getId());
				}
				
                // import files
                if (!empty($_FILES['import_file']['name'])){
                    $fileName = $_FILES['import_file']['tmp_name'];        
                    ini_set('auto_detect_line_endings', 1); 
                    
                    $errors = $this->_rateFactory->create()->import($model->getId(), $fileName);
                    foreach ($errors as $err){
                         $this->messageManager->addError($err);
                    }
                }			
				
				$this->_backendSession->setFormData(false);
				
				$msg = __('Shipping rates have been successfully saved');
                $this->messageManager->addSuccess($msg);
                if ($this->getRequest()->getParam('back')){
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
				}
                else {
                    return $resultRedirect->setPath('*/*');
                }
            } 
            catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_backendSession->setFormData($data);
                return $resultRedirect->setPath('*/*/edit', array('id' => $id));
            }	
            
        }
        
        $this->messageManager->addError(__('Unable to find a record to save'));
        return $resultRedirect->setPath('*/*');
	}
}
