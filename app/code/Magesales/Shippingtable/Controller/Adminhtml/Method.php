<?php
namespace Magesales\Shippingtable\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magesales\Shippingtable\Model\MethodFactory;
use Magesales\Shippingtable\Model\RateFactory;

abstract class Method extends Action
{
    protected $_title     = 'Custom Shipping Methods';
    protected $_modelName = 'method';	
	/**
     * @var RuleFactory
     */
    protected $_methodFactory;
	
	protected $_rateFactory;

    /**
     * @var Registry
     */
    protected $registry;
	protected $resultPageFactory;

    /**
     * @var Context
     */
    protected $context;
	protected $_backendSession;

    /**
     * {@inheritdoc}
     * @param RuleFactory    $ruleFactory
     * @param Registry       $registry
     * @param Context        $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        MethodFactory $methodFactory,
        Registry $registry,
        Context $context,
        ForwardFactory $resultForwardFactory,
		RateFactory $rateFactory,
		PageFactory $resultPageFactory
	) {
		parent::__construct($context);
        $this->_methodFactory = $methodFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultForwardFactory = $resultForwardFactory;
		$this->_rateFactory = $rateFactory;
		$this->resultPageFactory = $resultPageFactory;
        
    }
	
	/**
     * {@inheritdoc}
     * @param \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage
     * @return \Magento\Backend\Model\View\Result\Page\Interceptor
     */
	protected function _modifyStatus($status)
    {
        $ids = $this->getRequest()->getParam('methods');
        if ($ids && is_array($ids)){
            try {
                $this->_methodFactory->create()->massChangeStatus($ids, $status);
                $message = __('Total of %1 record(s) have been updated.', count($ids));
                $this->messageManager->addSuccess($message);
            } 
            catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        else {
            $this->messageManager->addError(__('Please select method(s).'));
        }
        
        return $this->_redirect('*/*');
    }     
    
    public function prepareForSave($model)
    {
        $fields = ['stores', 'cust_groups'];
        foreach ($fields as $f){
            // convert data from array to string
            $val = $model->getData($f);
            $model->setData($f, '');
            if (is_array($val)){
                // need commas to simplify sql query
                $model->setData($f, ',' . implode(',', $val) . ',');    
            } 
        }
        return true;
    }
    
    public function prepareForEdit($model)
    {
        $fields = ['stores', 'cust_groups'];
        foreach ($fields as $f){
            $val = $model->getData($f);
            if (!is_array($val)){
                $model->setData($f, explode(',', $val));    
            }        
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Magento_Sales::shipping_table');
    }
}
