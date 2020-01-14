<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Test\Unit\Model\Rate;

use Amasty\ShippingTableRates\Model\ConfigProvider;
use Amasty\ShippingTableRates\Model\Rate\ItemValidator;
use Amasty\ShippingTableRates\Test\Unit\Traits;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Model\Quote\Item;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ItemValidatorTest
 *
 * @see ItemValidator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ItemValidatorTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var ItemValidator
     */
    private $model;

    /**
     * @var ConfigProvider|MockObject
     */
    private $configProvider;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private $productRepository;

    protected function setUp()
    {
        $this->configProvider = $this->createPartialMock(
            ConfigProvider::class,
            [
                'isIgnoreVirtual',
                'getConfigurableSippingType',
                'getBundleShippingType',
                'isPromoAllowed',
                'isIncludingTax',
                'getSelectedWeightAttributeCode',
                'calculateVolumetricWeightWithShippingFactor'
            ]
        );
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        $this->model = $this->getObjectManager()->getObject(
            ItemValidator::class,
            [
                'configProvider' => $this->configProvider,
                'productRepository' => $this->productRepository,
            ]
        );
    }

    /**
     * @covers ItemValidator::isSkipItem
     */
    public function testIsSkipItem()
    {
        $item = $this->createPartialMock(Item::class, ['getParentItemId', 'getProduct']);
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);

        $item->expects($this->any())->method('getProduct')->willReturn($product);
        $item->expects($this->any())->method('getParentItemId')->willReturnOnConsecutiveCalls(true, false);
        $product->expects($this->once())->method('isVirtual')->willReturn(true);
        $this->configProvider->expects($this->once())->method('isIgnoreVirtual');

        $this->assertTrue($this->model->isSkipItem($item));
        $this->model->isSkipItem($item);
    }

    /**
     * @covers ItemValidator::isShouldProcessChildren
     */
    public function testIsShouldProcessChildren()
    {
        $item = $this->createPartialMock(Item::class, ['getHasChildren', 'getProduct']);
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);

        $item->expects($this->any())->method('getProduct')->willReturn($product);
        $item->expects($this->any())->method('getHasChildren')->willReturnOnConsecutiveCalls(false, true, true);
        $product->expects($this->any())->method('getTypeId')
            ->willReturnOnConsecutiveCalls(Configurable::TYPE_CODE, ProductType::TYPE_BUNDLE);
        $this->configProvider->expects($this->any())->method('getConfigurableSippingType')->willReturn(0);
        $this->configProvider->expects($this->any())->method('getBundleShippingType')->willReturn(2);

        $this->assertFalse($this->model->isShouldProcessChildren($item));
        $this->assertTrue($this->model->isShouldProcessChildren($item));
        $this->assertTrue($this->model->isShouldProcessChildren($item));
    }

    /**
     * @covers ItemValidator::getNotFreeQty
     */
    public function testGetNotFreeQty()
    {
        $item = $this->createPartialMock(Item::class, ['getQty']);

        $item->expects($this->once())->method('getQty')->willReturn(10);

        $this->assertEquals(5, $this->model->getNotFreeQty($item, 5));
        $this->assertEquals(10, $this->model->getNotFreeQty($item));
    }

    /**
     * @covers ItemValidator::getFreeQty
     */
    public function testGetFreeQty()
    {
        $item = $this->createPartialMock(Item::class, ['getQty', 'getFreeShipping']);

        $item->expects($this->any())->method('getQty')->willReturn(5);
        $item->expects($this->any())->method('getFreeShipping')->willReturnOnConsecutiveCalls(false, true);
        $this->configProvider->expects($this->any())->method('isPromoAllowed')->willReturn(true);

        $this->assertEquals(0, $this->model->getFreeQty($item));
        $this->assertEquals(5, $this->model->getFreeQty($item));
    }

    /**
     * @covers ItemValidator::getItemBasePrice
     */
    public function testGetItemBasePrice()
    {
        $item = $this->createPartialMock(Item::class, ['getBasePriceInclTax', 'getBasePrice']);

        $item->expects($this->any())->method('getBasePriceInclTax')->willReturn(10);
        $item->expects($this->any())->method('getBasePrice')->willReturn(5);
        $this->configProvider->expects($this->any())->method('isIncludingTax')->willReturnOnConsecutiveCalls(false, true);

        $this->assertEquals(5, $this->model->getItemBasePrice($item));
        $this->assertEquals(10, $this->model->getItemBasePrice($item));
    }

    /**
     * @covers ItemValidator::getItemWeight
     */
    public function testGetItemWeight()
    {
        $item = $this->createPartialMock(Item::class, ['getWeight', 'getProduct']);
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);

        $item->expects($this->any())->method('getWeight')->willReturn(5);
        $item->expects($this->any())->method('getProduct')->willReturn($product);
        $product->expects($this->any())->method('getId')->willReturn(2);
        $product->expects($this->any())->method('getData')->willReturn(6);
        $this->productRepository->expects($this->any())->method('getById')->willReturn($product);
        $this->configProvider->expects($this->any())->method('getSelectedWeightAttributeCode')->willReturn([1]);
        $this->configProvider->expects($this->any())->method('calculateVolumetricWeightWithShippingFactor')->willReturn(7);

        $this->assertEquals(7, $this->model->getItemWeight($item));
    }

    /**
     * @covers ItemValidator::prepareVolumeWeight
     */
    public function testPrepareVolumeWeight()
    {
        $this->assertEquals(0, $this->invokeMethod($this->model, 'prepareVolumeWeight', [1, []]));

        $product = $this->createMock(\Magento\Catalog\Model\Product::class);

        $this->productRepository->expects($this->any())->method('getById')->willReturn($product);
        $product->expects($this->any())->method('getData')->willReturn(6);

        $this->assertEquals(
            36.0,
            $this->invokeMethod($this->model, 'prepareVolumeWeight', [1, ['code', 'code2']])
        );
    }
}