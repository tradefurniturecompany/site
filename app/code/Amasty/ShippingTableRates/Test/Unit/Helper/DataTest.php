<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Test\Unit\Helper;

use Amasty\ShippingTableRates\Helper\Data;
use Amasty\ShippingTableRates\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class DataTest
 *
 * @see Data
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class DataTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    const REGIONS = [
        [
            'value' => [
                'country_id' => 0,
                'label' => 'testLabel'
            ]
        ]
    ];
    const ALL_COUNTRIES = [
            'value' => [
                'country_id' => 0,
                'label' => 'All/testLabel'
            ]
        ];

    /**
     * @covers Data::getDataFromZip
     *
     * @dataProvider getDataFromZipDataProvider
     *
     * @throws \ReflectionException
     */
    public function testGetDataFromZip($zip, $expectedResult)
    {
        /** @var Data $helper */
        $helper = $this->getObjectManager()->getObject(Data::class);
        $result = $helper->getDataFromZip($zip);
        $this->assertEquals($expectedResult, $result['district']);
        $this->assertArrayHasKey('district', $result);
    }

    /**
     * Data provider for getDataFromZip test
     * @return array
     */
    public function getDataFromZipDataProvider()
    {
        return [
            [85001, 85001],
            [72201, 72201],
            [-95814, 95814],
        ];
    }

    /**
     * @covers Data::_addCountriesToStates
     *
     * @throws \ReflectionException
     */
    public function testAddCountriesToStates()
    {
        /** @var Data $helper */
        $helper = $this->createPartialMock(Data::class, []);
        /** @var \Magento\Directory\Model\Country|MockObject $countryModel */
        $countryModel = $this->createMock(\Magento\Directory\Model\Country::class);
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|MockObject $collection */
        $collection = $this->createMock(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection::class);
        $collection->expects($this->any())->method('toOptionArray')->willReturn([]);

        $countryModel->expects($this->any())->method('getCollection')->willReturn($collection);
        /** @var \Magento\Framework\ObjectManagerInterface $objectManager|MockObject */
        $objectManager = $this->createMock(\Magento\Framework\ObjectManagerInterface::class, ['get']);
        $objectManager->expects($this->any())->method('get')->willReturn($countryModel);

        $this->setProperty($helper, '_objectManager', $objectManager, Data::class);

        $result = $this->invokeMethod($helper, '_addCountriesToStates', self::REGIONS);

        $this->assertEquals($result,self::ALL_COUNTRIES);
    }
}
