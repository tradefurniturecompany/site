<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Plugin;

use Magento\Payment\Helper\Data;

class Payrestriction {
    /**
     * @var \Amasty\Payrestriction\Model\Restrict
     */
    private $restrict;

    /**
     * Payrestriction constructor.
     * @param \Amasty\Payrestriction\Model\Restrict $restrict
     */
    public function __construct(
        \Amasty\Payrestriction\Model\Restrict $restrict
    ) {
        $this->restrict = $restrict;
    }

    /**
     * @param Data $subject
     * @param \Closure $proceed
     * @param null $store
     * @param null $quote
     * @return mixed
     */
    public function aroundGetStoreMethods(Data $subject, \Closure $proceed, $store = null, $quote = null)
    {
        $methods = $proceed($store, $quote);

        return $this->restrict->restrictMethods($methods, $quote);
    }
}
