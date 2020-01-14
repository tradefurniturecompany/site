<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Rate;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Rate;

class Delete extends Rate
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
            $this->messageManager->addError(__('Unable to find a rate to delete'));
            return $resultRedirect->setPath('shippingtable/method/index');
        }
        
        try {
            $rate = $this->_rateFactory->create()->load($id);
            $methodId = $rate->getMethodId();
            
            $rate->delete();
            
            $this->messageManager->addSuccess(__('Rate has been deleted'));
            return $resultRedirect->setPath('shippingtable/method/edit', ['id'=>$methodId, 'tab'=>'rates']);
        }
        catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('shippingtable/method/index');
        }
	}
}
