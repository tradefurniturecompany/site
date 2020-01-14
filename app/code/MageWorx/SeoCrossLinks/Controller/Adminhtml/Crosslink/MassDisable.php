<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

use MageWorx\SeoCrossLinks\Model\Crosslink;

class MassDisable extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 crosslinks have been disabled.';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while disabling crosslinks.';

    /**
     * @param Crosslink $crosslink
     * @return $this
     */
    protected function executeAction(Crosslink $crosslink)
    {
        $crosslink->setIsActive($this->getActionValue());
        $crosslink->save();
        return $this;
    }

    protected function getActionValue()
    {
        return Crosslink::STATUS_DISABLED;
    }
}
