<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

use MageWorx\SeoCrossLinks\Model\Crosslink;

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
     * @param $crosslink
     * @return $this
     */
    protected function executeAction(Crosslink $crosslink)
    {
        $crosslink->delete();
        return $this;
    }
}
