<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

use MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

class Create extends Customcanonical
{
    public function execute()
    {
        $title = __('New Custom Canonical URL');

        $this->_initAction()->_addBreadcrumb($title, $title);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Custom Canonical URLs'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }
}
