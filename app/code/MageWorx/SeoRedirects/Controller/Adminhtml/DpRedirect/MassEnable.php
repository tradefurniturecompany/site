<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\DpRedirect;

use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;

class MassEnable extends MassDisable
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 redirects have been enabled.';

    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while enabling redirects.';

    protected function getActionValue()
    {
        return DpRedirect::STATUS_ENABLED;
    }
}
