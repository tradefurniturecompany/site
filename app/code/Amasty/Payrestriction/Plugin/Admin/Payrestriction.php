<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Plugin\Admin;

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
     * @param \Magento\Payment\Block\Form\Container $subject
     * @param $methods
     * @return mixed
     */
    public function afterGetMethods(\Magento\Payment\Block\Form\Container $subject, $methods)
    {
        $quote = $subject->getQuote();

        return $this->restrict->restrictMethods($methods, $quote);
    }
}
