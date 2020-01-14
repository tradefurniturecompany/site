<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

use MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

class Delete extends Crosslink
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $crosslinkId = $this->getRequest()->getParam('crosslink_id');
        if ($crosslinkId) {
            try {
                /** @var \MageWorks\SeoCrossLinks\Model\Crosslinks $crosslink */
                $crosslink = $this->crosslinkFactory->create();
                $crosslink->load($crosslinkId);
                $keyword = $crosslink->getName();
                $crosslink->delete();
                $this->messageManager->addSuccess(__("The crosslink has been deleted."));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seocrosslinks_crosslink_on_delete',
                    ['status' => 'success', 'crosslink_id' => $crosslinkId, 'keyword' => $keyword]
                );
                $resultRedirect->setPath('mageworx_seocrosslinks/*/');
            } catch (\Exception $e) {
                $keyword = empty($keyword) ? '' : $keyword;
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seocrosslinks_crosslink_on_delete',
                    ['status' => 'fail', 'crosslink_id' => $crosslinkId, 'keyword' => $keyword]
                );
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('mageworx_seocrosslinks/*/edit', ['crosslink_id' => $crosslinkId]);

                return $resultRedirect;
            }

            return $resultRedirect;
        }
        $this->messageManager->addError(__('Crosslink not found.'));
        $resultRedirect->setPath('mageworx_seocrosslinks/*/');

        return $resultRedirect;
    }
}
