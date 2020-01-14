<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Block\Adminhtml\Crosslink\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * @method Tabs setTitle(\string $title)
 */
class Tabs extends WidgetTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('crosslink_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Crosslink Information'));
    }
}
