<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;

class NewAction extends \Amasty\Payrestriction\Controller\Adminhtml\Rule
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
