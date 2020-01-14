<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Model;

class Restrict
{
    /**
     * @var null
     */
    protected $allRules = null;

    /**
     * @var ResourceModel\Rule\Collection
     */
    protected $ruleCollection;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Amasty\CommonRules\Model\Validator\SalesRule
     */
    private $salesRuleValidator;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetaData;

    public function __construct(
        \Amasty\Payrestriction\Model\ResourceModel\Rule\Collection $ruleCollection,
        \Magento\Framework\App\State $appState,
        \Amasty\CommonRules\Model\Validator\SalesRule $salesRuleValidator,
        \Magento\Framework\App\ProductMetadataInterface $productMetaData
    ) {
        $this->ruleCollection = $ruleCollection;
        $this->appState = $appState;
        $this->salesRuleValidator = $salesRuleValidator;
        $this->productMetaData = $productMetaData;
    }

    /**
     * @param \Magento\Payment\Model\Method\AbstractMethod[] $paymentMethods
     * @param \Magento\Quote\Model\Quote|null $quote
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function restrictMethods($paymentMethods, $quote = null)
    {
        if (!$quote) {
            return $paymentMethods;
        }

        if ($this->productMetaData->getVersion() <= '2.2.1') {
            $quote->collectTotals();
        }

        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $quote->getShippingAddress();
        $items = $quote->getAllItems();
        $address->setItemsToValidateRestrictions($items);
        $hasBackOrders = false;
        $hasNoBackOrders = false;

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item){
            if ($item->getBackorders() > 0 ){
                $hasBackOrders = true;
            } else {
                $hasNoBackOrders = true;
            }

            if ($hasBackOrders && $hasNoBackOrders) {
                break;
            }
        }
        $paymentMethods = $this->validateMethods($paymentMethods, $address, $items);

        return $paymentMethods;
    }

    /**
     * @param \Magento\Payment\Model\Method\AbstractMethod[] $paymentMethods
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Magento\Quote\Model\Quote\Item[] $items
     *
     * @return \Magento\Payment\Model\Method\AbstractMethod[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateMethods($paymentMethods, $address, $items)
    {
        foreach ($paymentMethods as $key => $method) {
            /** @var \Amasty\Payrestriction\Model\Rule $rule */
            foreach ($this->getRules($address) as $rule) {
                if ($rule->restrict($method)
                    && $this->salesRuleValidator->validate($rule, $items)
                    && $rule->validate($address, $items)
                ) {
                    unset($paymentMethods[$key]);
                }
            }
        }

        return $paymentMethods;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return \Amasty\Payrestriction\Model\ResourceModel\Rule\Collection|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRules($address)
    {
        if (is_null($this->allRules)) {
            $this->allRules = $this->ruleCollection->addAddressFilter($address);

            if ($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                $this->allRules->addFieldToFilter('for_admin', 1);
            }

            $this->allRules->load();

            /** @var \Amasty\Payrestriction\Model\Rule $rule */
            foreach ($this->allRules as $rule){
                $rule->afterLoad();
            }
        }

        return $this->allRules;
    }
}
