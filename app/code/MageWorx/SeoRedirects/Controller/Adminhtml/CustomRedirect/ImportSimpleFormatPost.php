<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use Magento\Framework\Controller\ResultFactory;

class ImportSimpleFormatPost extends \Magento\Backend\App\Action
{
    /**
     * @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Import\CsvSimpleFormatHandler
     */
    protected $csvImportHandler;

    /**
     * ImportSimpleFormatPost constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Import\CsvSimpleFormatHandler $csvImportHandler
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Import\CsvSimpleFormatHandler $csvImportHandler
    ) {
        $this->csvImportHandler = $csvImportHandler;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {

            $file = $this->getRequest()->getFiles('import_seoredirects_custom_simple_file');

            if ($file && !empty($file['tmp_name'])) {
                try {
                    $this->csvImportHandler->importFromCsvFile($file);
                    $this->messageManager->addSuccessMessage(__('The custom redirects has been imported.'));

                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->addInvalidFileMessage();
                }

            } else {
                $this->addInvalidFileMessage();
            }

        } else {
            $this->addInvalidFileMessage();
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());

        return $resultRedirect;
    }

    /**
     * @return void
     */
    protected function addInvalidFileMessage()
    {
        $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
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
