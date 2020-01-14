<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

use MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink as  Crosslink;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoCrossLinks\Model\CrosslinkFactory;
use Magento\Framework\Registry;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Crosslink
{
    /**
     * backend session
     *
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * constructor
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param CrosslinkFactory $crosslinkFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        CrosslinkFactory $crosslinkFactory,
        Context $context
    ) {
    
        $this->backendSession = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($registry, $crosslinkFactory, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoCrossLinks::crosslinks');
    }

    public function execute()
    {
        $crosslinkId = $this->getRequest()->getParam('crosslink_id');
        /** @var \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink */
        $crosslink = $this->initCrosslink();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_SeoCrossLinks::crosslinks');
        $resultPage->getConfig()->getTitle()->set((__('Crosslink')));
        if ($crosslinkId) {
            $crosslink->load($crosslinkId);
            if (!$crosslink->getId()) {
                $this->messageManager->addError(__('The crosslink no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'mageworx_seocrosslinks/*/edit',
                    [
                        'crosslink_id' => $crosslink->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $title = $crosslink->getId() ? $crosslink->getName() : __('New Cross Link');
        $resultPage->getConfig()->getTitle()->append($title);
        $data = $this->backendSession->getData('mageworx_seocrosslinks_crosslink_data', true);
        if (!empty($data)) {
            $crosslink->setData($data);
        }
        return $resultPage;
    }
}
