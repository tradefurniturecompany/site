<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class ExportCsv extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Export\CsvDataProvider
     */
    protected $csvDataProvider;

    /**
     * ExportPost constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Export\CsvDataProvider $csvDataProvider
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Export\CsvDataProvider $csvDataProvider
    ) {
        $this->fileFactory     = $fileFactory;
        $this->csvDataProvider = $csvDataProvider;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        $ids = null;

        $params = $this->getRequest()->getParams();

        if (!empty($params['selected']) && is_array($params['selected'])) {
            $ids = $params['selected'];
            $ids = array_map('intval', $ids);
        }

        $content = $this->csvDataProvider->getContent($ids);

        return $this->fileFactory->create('custom_redirects_export_file.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoRedirects::import_export');
    }
}
