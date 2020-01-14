<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoAll\Helper;

class LandingPage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @return bool
     */
    public function isLandingPageEnabled()
    {
        return $this->_moduleManager->isEnabled('MageWorx_LandingPagesPro');
    }
}