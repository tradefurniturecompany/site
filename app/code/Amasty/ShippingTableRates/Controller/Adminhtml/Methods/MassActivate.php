<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Methods;

/**
 * Change status for group of Shipping Methods Action
 */
class MassActivate extends \Amasty\ShippingTableRates\Controller\Adminhtml\Methods
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
        $status = $this->getRequest()->getParam('activate');

        try {
            /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Method\Collection $collection */
            $collection = $this->methodCollectionFactory->create();
            $collection->addFieldToFilter('id', ['in' => $ids]);

            /** @var \Amasty\ShippingTableRates\Model\Method $item */
            foreach ($collection->getItems() as $item) {
                $item->setIsActive($status);
                $item->save();
            }
            $this->messageManager->addSuccessMessage(__('Record(s) have been updated.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t activate method(s) right now. Please review the log and try again. ') . $e->getMessage()
            );
            $this->logger->critical($e);
        }

        $this->_redirect('*/*/');
    }
}
