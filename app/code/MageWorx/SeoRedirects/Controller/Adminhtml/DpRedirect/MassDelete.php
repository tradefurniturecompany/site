<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\DpRedirect;

use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;

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
     * @param $dpRedirect
     * @return $this
     */
    protected function executeAction(DpRedirect $dpRedirect)
    {
        $dpRedirect->delete();

        return $this;
    }
}
