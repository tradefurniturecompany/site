<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBreadcrumbs\Helper;

/**
 * SEO Breadcrumbs category helper
 */
class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * We hardcoded the default category ID for magento 2.0.x compatibility:
     * @link https://github.com/magento/magento2/blob/2.0/app/code/Magento/Catalog/Setup/InstallData.php#L109
     * @see \Magento\Catalog\Helper\DefaultCategory For magento 2.1.x
     *
     * @return array
     */
    public function getRootAndDefaultIds()
    {
        return [
            \Magento\Catalog\Model\Category::TREE_ROOT_ID,
            2
        ];
    }
}
