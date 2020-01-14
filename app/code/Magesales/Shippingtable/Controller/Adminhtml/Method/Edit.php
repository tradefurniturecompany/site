<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Method;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Method;

class Edit extends Method
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        $model = $this->_methodFactory->create()->load($id);

		if ($id && !$model->getId())
		{
    		$this->messageManager->addError(__('Record does not exist'));
			$resultRedirect->setPath('*/*/');
			return;
		}
		$this->_backendSession = \Magento\Framework\App\ObjectManager::getInstance()
    ->get('Magento\Backend\Model\Session');
		$data = $this->_backendSession->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		else {
		    $this->prepareForEdit($model);
		}
		
		$this->registry->register('shippingtable_method', $model);

		$resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Shipping Table Rates') : __('New Shipping Table Rate'),
            $id ? __('Edit Shipping Table Rates') : __('New Shipping Table Rate')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Table Rates'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Shipping Table Rate'));

        $this->_view->loadLayout();
		
		$this->_view->getLayout()->initMessages();
        
		$this->_view->renderLayout();
	}
	
	protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Sales::shipping_table')
            ->addBreadcrumb(__('Shipping Table Rates'), __('Shipping Table Rates'))
            ->addBreadcrumb(__('Manage Shipping Table Rates'), __('Manage Shipping Table Rates'));
        return $resultPage;
    }
}
