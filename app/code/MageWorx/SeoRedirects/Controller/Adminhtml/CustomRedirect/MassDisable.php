<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class MassDisable extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 redirects have been disabled.';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while disabling redirects.';

    /**
     * @param CustomRedirect $customRedirect
     * @return $this
     */
    protected function executeAction(CustomRedirect $customRedirect)
    {
        $customRedirect->setStatus($this->getActionValue());
        $this->customRedirectRepository->save($customRedirect);

        return $this;
    }

    protected function getActionValue()
    {
        return CustomRedirect::STATUS_DISABLED;
    }
}
