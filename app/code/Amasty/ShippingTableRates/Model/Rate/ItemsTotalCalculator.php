<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\Rate;

use Amasty\ShippingTableRates\Model\ConfigProvider;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;

/**
 * Collect totals from request items for shipping rate
 */
class ItemsTotalCalculator
{
    /**
     * @var array
     */
    private $itemsTotals = [
        'not_free_price' => 0,
        'not_free_weight' => 0,
        'qty' => 0,
        'not_free_qty' => 0,
        'discount_amount' => 0,
    ];

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ItemValidator
     */
    private $itemValidator;

    public function __construct(
        ConfigProvider $configProvider,
        ItemValidator $itemValidator
    ) {
        $this->configProvider = $configProvider;
        $this->itemValidator = $itemValidator;
    }

    /**
     * @param RateRequest $request
     * @param int $shippingType
     *
     * @return array
     */
    public function execute($request, $shippingType)
    {
        $this->resetTotals();
        $afterDiscount = $this->configProvider->isAfterDiscount();
        $includingTax = $this->configProvider->isIncludingTax();

        /** @var Item $item */
        foreach ($request->getAllItems() as $item) {
            if ($this->itemValidator->isSkipItem($item)) {
                continue;
            }

            if ($this->itemValidator->isShouldProcessChildren($item)) {
                if (!$this->processChildItems($item, $shippingType)) {
                    continue;
                }
            } else {
                if (!$this->itemValidator->isSippingTypeValid($item, $shippingType)) {
                    continue;
                }

                $this->addItemTotal($item);
            }

            // Fix for correct calculation subtotal for shipping method
            if ($afterDiscount || $includingTax) {
                $this->itemsTotals['not_free_price'] += $item->getBaseDiscountTaxCompensationAmount();
            }
        }

        $this->afterCollect($request);

        return $this->itemsTotals;
    }

    /**
     * @param RateRequest $request
     */
    protected function afterCollect($request)
    {
        // fix magento bug
        if ($this->itemsTotals['not_free_qty'] > 0) {
            $request->setFreeShipping(false);
        }

        if ($this->configProvider->isAfterDiscount()) {
            $this->itemsTotals['not_free_price'] -= $this->itemsTotals['discount_amount'];
        }

        if ($this->itemsTotals['not_free_price'] < 0) {
            $this->itemsTotals['not_free_price'] = 0;
        }

        if ($request->getFreeShipping() && $this->configProvider->isPromoAllowed()) {
            $this->itemsTotals['not_free_price'] =
            $this->itemsTotals['not_free_weight'] = $this->itemsTotals['not_free_qty'] = 0;
        }

        foreach ($this->itemsTotals as &$value) {
            $value = round($value, 2);
        }
    }

    /**
     * Reset collected totals
     */
    public function resetTotals()
    {
        $this->itemsTotals = [
            'not_free_price' => 0,
            'not_free_weight' => 0,
            'qty' => 0,
            'not_free_qty' => 0,
            'discount_amount' => 0,
        ];
    }

    /**
     * @param Item $item
     * @param int $shippingType
     *
     * @return bool
     */
    public function processChildItems($item, $shippingType)
    {
        $flagOfPersist = false;
        foreach ($item->getChildren() as $child) {
            if (!$this->itemValidator->isSippingTypeValid($child, $shippingType)) {
                continue;
            }

            $flagOfPersist = true;
            $this->itemsTotals['discount_amount'] += $child->getBaseDiscountAmount();
        }

        switch ($item->getProduct()->getTypeId()) {
            case ProductType::TYPE_BUNDLE:
                if ($flagOfPersist == false) {
                    return false;
                }
                $this->addBundleItemTotal($item, $shippingType);

                break;
            case Configurable::TYPE_CODE:
                if ($flagOfPersist == false) {
                    return false;
                }
            //no-break
            default:  // for grouped and custom not simple products
                $this->addItemTotal($item);

                break;
        }

        return true;
    }

    /**
     * @param Item $item
     * @param int $shippingType
     */
    public function addBundleItemTotal($item, $shippingType)
    {
        $includingTax = $this->configProvider->isIncludingTax();
        $qty = $price = $weight = 0;
        foreach ($item->getChildren() as $child) {
            if (!$this->itemValidator->isSippingTypeValid($child, $shippingType)) {
                continue;
            }
            $itemQty = $child->getQty() * $item->getQty();
            $qty += $itemQty;
            $weight += $this->itemValidator->getItemWeight($child) * $itemQty;
            $price += $this->itemValidator->getItemBasePrice($child) * $itemQty;
        }

        if ($item->getProduct()->getWeightType() == 1) {
            $weight = $item->getWeight();
        }

        if ($item->getProduct()->getPriceType() == 1) {
            $price = $item->getBasePrice();
        }

        if ($item->getProduct()->getSkuType() == 1) {
            if ($includingTax) {
                $price = $item->getBasePriceInclTax();
            }
            $this->itemsTotals['discount_amount'] += $item->getBaseDiscountAmount();
        }

        $notFreeQty = $this->itemValidator->getNotFreeQty($item, $qty);
        $this->itemsTotals['qty'] += $qty;
        $this->itemsTotals['not_free_qty'] += $notFreeQty;
        $this->itemsTotals['not_free_price'] += $price;
        $this->itemsTotals['not_free_weight'] += $weight;
    }

    /**
     * @param Item $item
     */
    public function addItemTotal($item)
    {
        $notFreeQty = $this->itemValidator->getNotFreeQty($item);
        $this->itemsTotals['not_free_price'] += $this->itemValidator->getItemBasePrice($item) * $notFreeQty;
        $this->itemsTotals['not_free_weight'] += $this->itemValidator->getItemWeight($item) * $notFreeQty;
        $this->itemsTotals['qty'] += $item->getQty();
        $this->itemsTotals['not_free_qty'] += $notFreeQty;
        $this->itemsTotals['discount_amount'] += $item->getBaseDiscountAmount();
    }
}
