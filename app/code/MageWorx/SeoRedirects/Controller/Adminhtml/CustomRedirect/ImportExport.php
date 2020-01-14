<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use Magento\Framework\Controller\ResultFactory;

class ImportExport extends \MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->messageManager->addNoticeMessage(
            $this->_objectManager->get(\Magento\ImportExport\Helper\Data::class)->getMaxUploadSizeMessage()
        );

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('MageWorx_SeoRedirects::system_convert_seoredirects');
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(
                \MageWorx\SeoRedirects\Block\Adminhtml\Redirect\Custom\ImportExportHeader::class
            )
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(
                \MageWorx\SeoRedirects\Block\Adminhtml\Redirect\Custom\ImportExport::class
            )
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Custom SEO Redirects'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export Custom SEO Redirects'));

        return $resultPage;
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
