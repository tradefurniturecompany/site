<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Test\Unit\Model;

use Amasty\ShippingTableRates\Model\Rate;
use Amasty\ShippingTableRates\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class RateTest
 *
 * @see Rate
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class RateTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Rate::getShippingTypes
     *
     * @throws \ReflectionException
     */
    public function testGetShippingTypes()
    {
        /** @var Rate $model */
        $model = $this->createPartialMock(Rate::class, []);
        /** @var \Magento\Quote\Model\Quote\Item|MockObject $item */
        $item = $this->getObjectManager()->getObject(\Magento\Quote\Model\Quote\Item::class);
        $item->setProductId(1);
        /** @var \Magento\Catalog\Model\Product|MockObject $product */
        $product = $this->createPartialMock(\Magento\Catalog\Model\Product::class, ['getAmShippingType']);
        $product->expects($this->any())->method('getAmShippingType')->willReturn('delivery');
        /** @var \Magento\Catalog\Model\Product $productRepository */
        $productRepository = $this->createPartialMock(\Magento\Catalog\Model\Product::class, ['getById']);
        $productRepository->expects($this->any())->method('getById')->willReturn($product);

        $this->setProperty($model, 'productRepository', $productRepository, Rate::class);

        $product->expects($this->exactly(2))->method('getAmShippingType');
        $this->invokeMethod($model, 'getShippingTypes', [$item]);
    }
}
