<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Test\Unit\Model\Rate;

use Amasty\ShippingTableRates\Model\ConfigProvider;
use Amasty\ShippingTableRates\Model\Rate\ItemsTotalCalculator;
use Amasty\ShippingTableRates\Model\Rate\ItemValidator;
use Amasty\ShippingTableRates\Test\Unit\Traits;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ItemsTotalCalculatorTest
 *
 * @see ItemsTotalCalculator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ItemsTotalCalculatorTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var ItemsTotalCalculator
     */
    private $model;

    /**
     * @var ConfigProvider|MockObject
     */
    private $configProvider;

    /**
     * @var ItemValidator|MockObject
     */
    private $itemValidator;

    protected function setUp()
    {
        $this->configProvider = $this->createPartialMock(
            ConfigProvider::class,
            ['isSippingTypeValid', 'isAfterDiscount', 'isIncludingTax', 'isPromoAllowed']
        );
        $this->itemValidator =  $this->createMock(ItemValidator::class);

        $this->configProvider->expects($this->any())->method('isIncludingTax')->willReturn(true);
        $this->itemValidator->expects($this->any())->method('getNotFreeQty')->willReturn(44);
        $this->itemValidator->expects($this->any())->method('getItemBasePrice')->willReturn(100);
        $this->itemValidator->expects($this->any())->method('getItemWeight')->willReturn(5);

        $this->model = $this->getObjectManager()->getObject(
            ItemsTotalCalculator::class,
            [
                'configProvider' => $this->configProvider,
                'itemValidator' => $this->itemValidator
            ]
        );
    }

    /**
     * @covers ItemsTotalCalculator::execute
     * @dataProvider executeDataProvider
     */
    public function testExecute($skip, $shouldProcessChildren, $processChildren, $isShippingTypeValid, $result)
    {
        $item = $this->createPartialMock(Item::class, ['getBaseDiscountTaxCompensationAmount']);
        $this->model = $this->createPartialMock(
            ItemsTotalCalculator::class,
            ['processChildItems']
        );
        $request = $this->createPartialMock(RateRequest::class, ['getAllItems']);

        $this->configProvider->expects($this->any())->method('isAfterDiscount')->willReturn(true);
        $request->expects($this->any())->method('getAllItems')->willReturn([$item]);
        $this->itemValidator->expects($this->any())->method('isSkipItem')->willReturn($skip);
        $this->itemValidator->expects($this->any())->method('isShouldProcessChildren')->willReturn($shouldProcessChildren);
        $this->itemValidator->expects($this->any())->method('isSippingTypeValid')->willReturn($isShippingTypeValid);
        $this->model->expects($this->any())->method('processChildItems')->willReturn($processChildren);
        $item->expects($this->any())->method('getBaseDiscountTaxCompensationAmount')->willReturn(5);

        $this->setProperty($this->model, 'configProvider', $this->configProvider,ItemsTotalCalculator::class);
        $this->setProperty($this->model, 'itemValidator', $this->itemValidator, ItemsTotalCalculator::class);

        $this->assertEquals($result, $this->model->execute($request, 'test'));
    }

    /**
     * Data provider for execute test
     * @return array
     */
    public function executeDataProvider()
    {
        $defaultResult = [
            'not_free_price' => 0.0,
            'not_free_weight' => 0.0,
            'qty' => 0.0,
            'not_free_qty' => 0.0,
            'discount_amount' => 0.0
        ];
        $changedResult = [
            'not_free_price' => 5.0,
            'not_free_weight' => 0.0,
            'qty' => 0.0,
            'not_free_qty' => 0.0,
            'discount_amount' => 0.0
        ];
        return [
            [true, false, false, false, $defaultResult],
            [false, true, false, false, $defaultResult],
            [false, false, false, false, $defaultResult],
            [false, false, true, false, $defaultResult],
            [false, true, true, true, $changedResult],
        ];
    }

    /**
     * @covers ItemsTotalCalculator::afterCollect
     * @dataProvider afterCollectDataProvider
     */
    public function testAfterCollect($data, $result, $isPromoOrFree)
    {
        $request = $this->createPartialMock(RateRequest::class, ['setFreeShipping', 'getFreeShipping']);

        $request->expects($this->once())->method('setFreeShipping');
        $this->configProvider->expects($this->once())->method('isAfterDiscount')->willReturn(true);
        if ($isPromoOrFree) {
            $request->expects($this->once())->method('getFreeShipping')->willReturn(true);
            $this->configProvider->expects($this->once())->method('isPromoAllowed')->willReturn(true);
        }

        $this->setProperty($this->model, 'itemsTotals', $data, ItemsTotalCalculator::class);

        $this->invokeMethod($this->model, 'afterCollect', [$request]);

        $this->assertEquals(
            $result,
            $this->getProperty($this->model, 'itemsTotals', ItemsTotalCalculator::class)
        );
    }

    /**
     * Data provider for afterCollect test
     * @return array
     */
    public function afterCollectDataProvider()
    {
        return [
            [
                [
                    'not_free_qty' => 5,
                    'not_free_price' => 10,
                    'discount_amount' => 4,
                ],
                [
                    'not_free_qty' => 5.0,
                    'not_free_price' => 6.0,
                    'discount_amount' => 4.0,
                ],
                false
            ],
            [
                [
                    'not_free_qty' => 5,
                    'not_free_price' => 6,
                    'discount_amount' => 10,
                ],
                [
                    'not_free_qty' => 5.0,
                    'not_free_price' => 0.0,
                    'discount_amount' => 10.0,
                ],
                false
            ],
            [
                [
                    'not_free_qty' => 5,
                    'not_free_price' => 6,
                    'discount_amount' => 10,
                ],
                [
                    'not_free_qty' => 0.0,
                    'not_free_price' => 0.0,
                    'discount_amount' => 10.0,
                    'not_free_weight' => 0.0
                ],
                true
            ]
        ];
    }

    /**
     * @covers ItemsTotalCalculator::processChildItems
     * @dataProvider processChildItemsDataProvider
     */
    public function testProcessChildItems($type, $result, $child = [])
    {
        $item = $this->createPartialMock(Item::class, ['getChildren', 'getProduct']);
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        if ($child) {
            $child = $this->getMockBuilder(\Magento\Quote\Model\Quote\Item\AbstractItem::class)
                ->setMethods(['getBaseDiscountAmount'])
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();
            $child->expects($this->any())->method('getBaseDiscountAmount')->willReturn(10);
            $child = [$child];
        }

        $this->itemValidator->expects($this->any())->method('isSippingTypeValid')->willReturn(true);
        $item->expects($this->any())->method('getChildren')->willReturn($child);
        $item->expects($this->any())->method('getProduct')->willReturn($product);
        $product->expects($this->any())->method('getTypeId')->willReturn($type);
        $this->assertEquals($result, $this->model->processChildItems($item, 'test'));
    }

    /**
     * Data provider for processChildItems test
     * @return array
     */
    public function processChildItemsDataProvider()
    {
        return [
            [ProductType::TYPE_BUNDLE, true, true],
            [ProductType::TYPE_BUNDLE, false],
            [Configurable::TYPE_CODE, true, true],
            [Configurable::TYPE_CODE, false],
            ['default', true],
        ];
    }

    /**
     * @covers ItemsTotalCalculator::addBundleItemTotal
     */
    public function testAddBundleItemTotal()
    {
        $result = [
            'not_free_price' => 20,
            'not_free_weight' => 2,
            'qty' => 0,
            'not_free_qty' => 44,
            'discount_amount' => 15,
        ];
        $item = $this->getMockBuilder(Item::class)
            ->setMethods(
                ['getChildren', 'getProduct', 'getQty', 'getWeight', 'getBasePrice', 'getBasePriceInclTax', 'getBaseDiscountAmount']
            )
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $product = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->setMethods(['getWeightType', 'getPriceType', 'getSkuType'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $child = $this->getMockBuilder(\Magento\Quote\Model\Quote\Item\AbstractItem::class)
            ->setMethods(['getQty'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $item->expects($this->any())->method('getChildren')->willReturn($child);
        $item->expects($this->any())->method('getQty')->willReturn(10);
        $item->expects($this->any())->method('getProduct')->willReturn($product);
        $item->expects($this->any())->method('getWeight')->willReturn(2);
        $item->expects($this->any())->method('getBasePrice')->willReturn(50);
        $item->expects($this->any())->method('getBasePriceInclTax')->willReturn(20);
        $item->expects($this->any())->method('getBaseDiscountAmount')->willReturn(15);
        $child->expects($this->any())->method('getQty')->willReturn(20);
        $product->expects($this->any())->method('getWeightType')->willReturn(1);
        $product->expects($this->any())->method('getPriceType')->willReturn(1);
        $product->expects($this->any())->method('getSkuType')->willReturn(1);

        $this->model->addBundleItemTotal($item, 'test');

        $this->assertEquals(
            $result,
            $this->getProperty($this->model, 'itemsTotals', ItemsTotalCalculator::class)
        );
    }

    /**
     * @covers ItemsTotalCalculator::addItemTotal
     */
    public function testAddItemTotal()
    {
        $result = [
            'not_free_price' => 4400,
            'not_free_weight' => 220,
            'qty' => 10,
            'not_free_qty' => 44,
            'discount_amount' => 15
        ];
        $item = $this->createPartialMock(Item::class, ['getQty', 'getBaseDiscountAmount']);

        $item->expects($this->any())->method('getBaseDiscountAmount')->willReturn(15);
        $item->expects($this->any())->method('getQty')->willReturn(10);

        $this->model->addItemTotal($item);

        $this->assertEquals(
            $result,
            $this->getProperty($this->model, 'itemsTotals', ItemsTotalCalculator::class)
        );
    }
}
