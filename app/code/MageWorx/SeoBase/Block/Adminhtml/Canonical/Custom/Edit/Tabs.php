<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Block\Adminhtml\Canonical\Custom\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('mageworx_seobase_customcanonical_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Custom Canonical URL'));
    }
}
