<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Test\Unit\Model\Cart;

use Amasty\ShippingTableRates\Helper\Data;
use Amasty\ShippingTableRates\Model\Cart\ShippingMethodConverter;
use Amasty\ShippingTableRates\Test\Unit\Traits;
use Amasty\ShippingTableRates\Model\ResourceModel\Label\CollectionFactory;
use Amasty\ShippingTableRates\Model\MethodFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ShippingMethodConverterTest
 *
 * @see ShippingMethodConverter
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ShippingMethodConverterTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers ShippingMethodConverter::afterModelToDataObject
     */
    public function testAfterModelToDataObject()
    {
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $store = $this->getMockBuilder(\Magento\Store\Api\Data\StoreInterface::class)
            ->setMethods(['getId', 'getBaseUrl'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $labelCollectionFactory = $this->createMock(CollectionFactory::class);
        $labelCollection = $this->createMock(\Amasty\ShippingTableRates\Model\ResourceModel\Label\Collection::class);
        $label = $this->getMockBuilder(\Amasty\ShippingTableRates\Model\Label::class)
            ->setMethods(['getComment'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $methodFactory = $this->createMock(MethodFactory::class);
        $method = $this->createMock(\Amasty\ShippingTableRates\Model\Method::class);
        $helperData = $this->createMock(Data::class);
        $attributesFactory = $this->createMock(ExtensionAttributesFactory::class);
        $attribute = $this->getMockBuilder(\Magento\Quote\Api\Data\ShippingMethodExtensionInterface::class)
            ->setMethods(['setAmstartesComment'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $subject = $this->createMock(\Magento\Quote\Model\Cart\ShippingMethodConverter::class);
        $result = $this->createMock(\Magento\Quote\Api\Data\ShippingMethodInterface::class);

        $model = $this->getObjectManager()->getObject(
            ShippingMethodConverter::class,
            [
                'storeManager' => $storeManager,
                'labelCollectionFactory' => $labelCollectionFactory,
                'methodFactory' => $methodFactory,
                'helperData' => $helperData,
                'attributesFactory' => $attributesFactory,
            ]
        );

        $result->expects($this->once())->method('getCarrierCode')->willReturnOnConsecutiveCalls('amstrates');
        $result->expects($this->once())->method('getMethodCode')->willReturn('amstratestest');
        $result->expects($this->once())->method('getExtensionAttributes')->willReturn(null);
        $storeManager->expects($this->any())->method('getStore')->willReturn($store);
        $store->expects($this->once())->method('getId')->willReturn(1);
        $store->expects($this->once())->method('getBaseUrl')->willReturn('http://test.com/');
        $labelCollectionFactory->expects($this->once())->method('create')->willReturn($labelCollection);
        $labelCollection->expects($this->once())->method('addFiltersByMethodIdStoreId')->willReturn($labelCollection);
        $labelCollection->expects($this->once())->method('getLastItem')->willReturn($label);
        $label->expects($this->any())->method('getComment')->willReturn('labelcomment');
        $methodFactory->expects($this->once())->method('create')->willReturn($method);
        $method->expects($this->once())->method('load')->willReturn($method);
        $method->expects($this->never())->method('getComment')->willReturn('methodcomment');
        $method->expects($this->once())->method('getCommentImg')->willReturn('{IMG}');
        $helperData->expects($this->once())->method('escapeHtml')->willReturnArgument(0);
        $attributesFactory->expects($this->once())->method('create')->willReturn($attribute);

        $model->afterModelToDataObject($subject, $result);
    }
}
