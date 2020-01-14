<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Block\Sitemap;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Store\Block\Switcher;

/**
 * Store switcher block
 */
class StoreSwitcher extends Switcher
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        array $data = []
    ) {
    
        parent::__construct($context, $postDataHelper, $data);
    }
}
