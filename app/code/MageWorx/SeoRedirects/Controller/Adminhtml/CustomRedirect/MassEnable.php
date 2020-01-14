<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

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

    /**
     * @return int
     */
    protected function getActionValue()
    {
        return CustomRedirect::STATUS_ENABLED;
    }
}
