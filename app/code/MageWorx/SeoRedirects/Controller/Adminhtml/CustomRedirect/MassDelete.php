<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class MassDelete extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 record(s) have been deleted';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while deleting record(s).';

    /**
     * @param $customRedirect
     * @return $this
     */
    protected function executeAction(CustomRedirect $customRedirect)
    {
        $this->customRedirectRepository->delete($customRedirect);

        return $this;
    }
}
