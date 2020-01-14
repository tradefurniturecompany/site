<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\Rate;

use Amasty\ShippingTableRates\Model\ConfigProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Validate Cart Items for Shipping Rate
 */
class ItemValidator
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(ConfigProvider $configProvider, ProductRepositoryInterface $productRepository)
    {
        $this->configProvider = $configProvider;
        $this->productRepository = $productRepository;
    }

    /**
     * Is Items cant be processed
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return bool
     */
    public function isSkipItem($item)
    {
        if ($item->getParentItemId()) {
            return true;
        }

        return $item->getProduct()->isVirtual() && $this->configProvider->isIgnoreVirtual();
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $shippingType
     *
     * @return bool
     */
    public function isSippingTypeValid($item, $shippingType)
    {
        return $shippingType == 0
            || $this->productRepository->getById($item->getProductId())->getAmShippingType() == $shippingType;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return bool
     */
    public function isShouldProcessChildren($item)
    {
        if (!$item->getHasChildren()) {
            return false;
        }
        $product = $item->getProduct();
        $typeId = $product->getTypeId();
        if ($typeId === ProductType::TYPE_BUNDLE) {
            $bundleType = $this->configProvider->getBundleShippingType();

            return ($bundleType === 2 || ($bundleType === 0 && $product->getShipmentType() == '1'));
        }

        return $typeId === Configurable::TYPE_CODE && $this->configProvider->getConfigurableSippingType() === 0;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float|null $qty
     *
     * @return float
     */
    public function getNotFreeQty($item, $qty = null)
    {
        if ($qty === null) {
            $qty = $item->getQty();
        }

        return $qty - $this->getFreeQty($item);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return float
     */
    public function getFreeQty($item)
    {
        if ($item->getFreeShipping() && $this->configProvider->isPromoAllowed()) {
            return $item->getQty();
        }

        return 0;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return float
     */
    public function getItemBasePrice($item)
    {
        if ($this->configProvider->isIncludingTax()) {
            return $item->getBasePriceInclTax();
        }

        return $item->getBasePrice();
    }

    /**
     * The method get value of weight depends on attribute
     * from 'volumetric weight attribute'
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return float
     */
    public function getItemWeight($item = null)
    {
        $calculatedWeight = $item ? $item->getWeight() : 0;
        $weightAttributeCodes = $this->configProvider->getSelectedWeightAttributeCode();

        if (!empty($weightAttributeCodes)) {
            $productId = $item->getProduct()->getId();
            $volumeWeight = $this->prepareVolumeWeight($productId, $weightAttributeCodes);
            $volumetricWeight = $this->configProvider->calculateVolumetricWeightWithShippingFactor($volumeWeight);

            if ((float)$volumetricWeight > (float)$calculatedWeight) {
                $calculatedWeight = $volumetricWeight;
            }
        }

        return $calculatedWeight;
    }

    /**
     * The method gathers attribute from product
     *
     * @param int $productId
     * @param array $weightAttributeCodes
     *
     * @return float|int
     */
    private function prepareVolumeWeight($productId = 0, $weightAttributeCodes = [])
    {
        if (empty($weightAttributeCodes)) {
            return 0;
        }

        $product = $this->productRepository->getById($productId);
        $weightAttributeCode = array_shift($weightAttributeCodes);
        $volumeWeight = $product->getData($weightAttributeCode);

        if (!empty($weightAttributeCodes)) {
            foreach ($weightAttributeCodes as $attributeCode) {
                $volumeWeight *= (float)$product->getData($attributeCode);
            }
        }

        return $volumeWeight;
    }
}
