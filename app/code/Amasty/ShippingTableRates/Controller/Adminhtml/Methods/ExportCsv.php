<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Controller\Adminhtml\Methods;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Export Shipping Rates of Method Action
 */
class ExportCsv extends \Magento\Backend\App\Action
{
    protected $_fileFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $fileName = 'rates.csv';
        $this->_view->loadLayout();

        /**
         * @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock
         */
        $exportBlock = $this->_view->getLayout()
            ->getChildBlock('adminhtml.amstrates.methods.rates.grid', 'grid.export');

        return $this->_fileFactory->create($fileName, $exportBlock->getCsv(), DirectoryList::VAR_DIR);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShippingTableRates::amstrates');
    }
}
