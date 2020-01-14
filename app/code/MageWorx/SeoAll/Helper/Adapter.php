<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoAll\Helper;

class Adapter extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SEOXTEMPLATES_EXTENSION_NAME = 'MageWorx_SeoXTemplates';

    /**
     * @return bool
     */
    public function isSeoXTemplatesAvailable()
    {
        return $this->isModuleOutputEnabled(self::SEOXTEMPLATES_EXTENSION_NAME);
    }
}
