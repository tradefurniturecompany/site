<?php
/**
 * @category  Trustedshops
 * @package   Trustedshops\Trustedshops
 * @author    Trusted Shops GmbH
 * @copyright 2016 Trusted Shops GmbH
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.trustedshops.de/
 */

namespace Trustedshops\Trustedshops\Block\Review;

use Trustedshops\Trustedshops\Block\Review;

class Tab extends Review
{
    /**
     * only display review code if it is active
     *
     * @return bool|string
     */
    protected function _toHtml()
    {
        if (!$this->isActive()) {
            return false;
        }
        return parent::_toHtml();
    }
}
