<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Controller\Adminhtml\ExtensionInfo;

use Magento\Backend\App\Action\Context;
use MageWorx\Info\Helper\Data;

class Update extends \Magento\Backend\App\Action
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Update constructor.
     *
     * @param Data $helper
     * @param Context $context
     */
    public function __construct(
        Data $helper,
        Context $context
    ) {
        parent::__construct(
            $context
        );
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->helper->checkExtensionListUpdate(true);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('adminhtml/system_config/edit/section/mageworx_extensions');

        return $resultRedirect;
    }
}
