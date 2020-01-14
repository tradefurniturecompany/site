<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Category\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * @method Tabs setMetaTitle(\string $title)
 */
class Tabs extends WidgetTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('template_category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Template Information'));
    }
}
