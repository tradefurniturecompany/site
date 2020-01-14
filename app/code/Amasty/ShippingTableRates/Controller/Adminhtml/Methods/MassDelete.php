<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Methods;

/**
 * Delete group of Shipping Methods Action
 */
class MassDelete extends \Amasty\ShippingTableRates\Controller\Adminhtml\Methods
{
    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory
     */
    private $methodCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory $methodCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->methodCollectionFactory = $methodCollectionFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $ids = $this->getRequest()->getParam('ids');

        try {
            /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Method\Collection $collection */
            $collection = $this->methodCollectionFactory->create();
            $collection->addFieldToFilter('id', ['in' => $ids]);
            $collection->walk('delete');
            $this->messageManager->addSuccessMessage(__('Method(s) were successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete method(s) right now. Please review the log and try again. ') . $e->getMessage()
            );
            $this->logger->critical($e);
        }

        $this->_redirect('*/*/');
    }
}
