<?php
namespace Magesales\Shippingtable\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magesales\Shippingtable\Model\RateFactory;
use Magento\Framework\View\Result\PageFactory;

abstract class Rate extends Action
{
    /**
     * @var RuleFactory
     */
    protected $_rateFactory;

	protected $resultPageFactory;
    /**
     * @var Registry
     */
    protected $registry;


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
        RateFactory $rateFactory,
        Registry $registry,
        Context $context,
        ForwardFactory $resultForwardFactory,
		PageFactory $resultPageFactory,
		\Magento\Backend\Model\Auth\Session $backendSession	
    ) {
		parent::__construct($context);
        $this->_rateFactory = $rateFactory;
        $this->registry = $registry;
        $this->context = $context;
		$this->resultForwardFactory = $resultForwardFactory;
		$this->resultPageFactory = $resultPageFactory;
	}

    protected function initModel()
    {
        $model = $this->_rateFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('shippingtable_rate', $model);

        return $model;
    }
	
	/**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Magento_Sales::shipping_table');
    }
}
