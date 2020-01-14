<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

use MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;
use MageWorx\SeoBase\Model\CustomCanonical as CustomCanonicalModel;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Customcanonical
{
    public function execute()
    {
        $title = __('Edit Custom Canonical URL');
        $id    = (int)$this->getRequest()->getParam('id');

        try {
            $customCanonical = $this->customCanonicalRepository->getById($id);
            $this->coreRegistry->register(CustomCanonicalModel::CURRENT_CUSTOM_CANONICAL, $customCanonical);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('*/*/index');

            return;
        }

        $this->_initAction()->_addBreadcrumb($title, $title);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Custom Canonical URLs'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }
}
