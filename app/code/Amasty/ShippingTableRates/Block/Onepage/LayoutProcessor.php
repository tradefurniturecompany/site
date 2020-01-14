<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Onepage;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Additional Layout Processor for Checkout Page
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Replace template sor shipping Address
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function process($jsLayout)
    {
        if ($this->isCompatibleCheckout()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['template'] = 'Amasty_ShippingTableRates/shipping';
        }

        return $jsLayout;
    }

    /**
     * Check checkout is compatible with Table Rates
     *
     * @return bool
     */
    private function isCompatibleCheckout()
    {
        return !($this->moduleManager->isEnabled('Magestore_OneStepCheckout')
            || ($this->moduleManager->isEnabled('IWD_Opc')
                && $this->scopeConfig->getValue('iwd_opc/general/enable'))
            || $this->moduleManager->isEnabled('Mageplaza_Osc'));
    }
}
