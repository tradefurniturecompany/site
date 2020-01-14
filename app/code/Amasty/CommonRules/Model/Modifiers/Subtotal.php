<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Modifiers;

class Subtotal implements \Amasty\CommonRules\Model\Modifiers\ModifierInterface
{
    /**
     * @var string
     */
    protected $sectionConfig = '';

    /**
     * @var \Amasty\CommonRules\Model\Config
     */
    private $config;

    /**
     * Subtotal constructor.
     * @param \Amasty\CommonRules\Model\Config $config
     */
    public function __construct(\Amasty\CommonRules\Model\Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $object
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function modify($object)
    {
        /** @var \Magento\Quote\Model\Quote\Address $tempObject */
        $tempObject = clone $object;

        $subtotal = $tempObject->getSubtotal();
        $baseSubtotal = $tempObject->getBaseSubtotal();

        if ($this->config->getTaxIncludeConfig($this->getSectionConfig())) {
            $subtotal += $tempObject->getTaxAmount();
            $baseSubtotal += $tempObject->getBaseTaxAmount();
        }

        if ($this->config->getUseSubtotalConfig($this->getSectionConfig())) {
            $subtotal += $tempObject->getDiscountAmount();
            $baseSubtotal += $tempObject->getBaseDiscountAmount();
        }

        $tempObject->setSubtotal($subtotal);
        $tempObject->setBaseSubtotal($baseSubtotal);
        $tempObject->setPackageValueWithDiscount($baseSubtotal);

        return $tempObject;
    }

    /**
     * @param $sectionConfig
     * @return $this
     */
    public function setSectionConfig($sectionConfig)
    {
        $this->sectionConfig = $sectionConfig;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionConfig()
    {
        return $this->sectionConfig;
    }
}
