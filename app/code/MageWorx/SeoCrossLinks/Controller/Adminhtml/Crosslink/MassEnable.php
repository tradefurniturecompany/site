<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

use MageWorx\SeoCrossLinks\Model\Crosslink;

class MassEnable extends MassDisable
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 crosslinks have been enabled.';

    /**
     * @var string
     */
    protected $errorMessage   = 'An error occurred while enabling crosslinks.';

    protected function getActionValue()
    {
        return Crosslink::STATUS_ENABLED;
    }
}
