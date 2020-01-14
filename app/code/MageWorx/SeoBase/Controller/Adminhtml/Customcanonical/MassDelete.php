<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

use MageWorx\SeoBase\Model\ResourceModel\CustomCanonical\Collection as CustomCanonicalCollection;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\LocalizedException;

class MassDelete extends AbstractMassAction
{
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while deleting record(s).';

    /**
     * @param CustomCanonicalCollection $collection
     * @return ResultRedirect
     */
    protected function massAction($collection)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $recordDeleted  = 0;

        foreach ($collection->getAllIds() as $customCanonicalId) {
            try {
                $this->customCanonicalRepository->deleteById($customCanonicalId);
                $recordDeleted++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        if ($recordDeleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $recordDeleted));
        }

        $resultRedirect->setPath($this->redirectUrl);

        return $resultRedirect;
    }
}
