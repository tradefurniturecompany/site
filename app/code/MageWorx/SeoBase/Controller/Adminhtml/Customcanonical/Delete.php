<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

class Delete extends Customcanonical
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->customCanonicalRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Custom Canonical has been deleted.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the Custom Canonical right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Custom Canonical is not found.'));
        }
        $resultRedirect->setPath('mageworx/*/index');

        return $resultRedirect;
    }
}
