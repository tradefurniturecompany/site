<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

namespace Amasty\Payrestriction\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Amasty\Payrestriction\Controller\Adminhtml\Rule;
use Magento\Framework\View\Result\PageFactory;

abstract class AbstractMassAction extends \Amasty\Payrestriction\Controller\Adminhtml\Rule
{
    protected $filter;
    protected $collectionFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Amasty\Payrestriction\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $resultPageFactory);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return $this->_redirect('*/*');
    }

    abstract protected function massAction($collection);
}
