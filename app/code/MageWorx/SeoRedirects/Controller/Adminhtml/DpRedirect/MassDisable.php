<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\DpRedirect;

use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;

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
     * @param DpRedirect $dpRedirect
     * @return $this
     */
    protected function executeAction(DpRedirect $dpRedirect)
    {
        $dpRedirect->setStatus($this->getActionValue());
        $dpRedirect->save();

        return $this;
    }

    protected function getActionValue()
    {
        return DpRedirect::STATUS_DISABLED;
    }
}
