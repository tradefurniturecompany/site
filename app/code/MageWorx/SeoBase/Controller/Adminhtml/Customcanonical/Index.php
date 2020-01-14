<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

use MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

class Index extends Customcanonical
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Custom Canonical URLs'));
        $this->_view->renderLayout();
    }
}
