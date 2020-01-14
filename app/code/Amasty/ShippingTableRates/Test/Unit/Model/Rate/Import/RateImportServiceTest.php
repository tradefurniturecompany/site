<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Test\Unit\Model\Rate\Import;

use Amasty\ShippingTableRates\Model\Rate\Import\RateImportService;
use Amasty\ShippingTableRates\Test\Unit\Traits;
use Amasty\ShippingTableRates\Model\Rate;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class RateImportServiceTest
 *
 * @see RateImportService
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class RateImportServiceTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers RateImportService::_setDefaultLineValues
     */
    public function testSetDefaultLineValues()
    {
        $line = [RateImportService::PRICE_TO => 0, RateImportService::WEIGHT_TO => 0, RateImportService::QTY_TO => 0];
        $result = [
            RateImportService::PRICE_TO => Rate::MAX_VALUE,
            RateImportService::WEIGHT_TO => Rate::MAX_VALUE,
            RateImportService::QTY_TO => Rate::MAX_VALUE
        ];
        $model = $this->getObjectManager()->getObject(RateImportService::class);
        $this->assertEquals($result, $this->invokeMethod($model, '_setDefaultLineValues', [$line]));
    }

    /**
     * @covers RateImportService::_prepareLineTypes
     */
    public function testPrepareLineTypes()
    {
        $line = [RateImportService::SHIPPING_TYPE => 'All, 1, 2'];
        $result = [
          'line' => [11 => 0],
          'err' => ['Line #5: invalid type code  1']
        ];
        $model = $this->getObjectManager()->getObject(RateImportService::class);
        $this->assertEquals($result, $this->invokeMethod($model, '_prepareLineTypes', [$line, [], 5, [2]]));
    }

    /**
     * @covers RateImportService::returnErrors
     */
    public function testReturnErrors()
    {
        $data = [['test1test2test3test4test5test6test7test8']];
        $rateResource = $this->createMock(\Amasty\ShippingTableRates\Model\ResourceModel\Rate::class);
        $model = $this->getObjectManager()->getObject(
            RateImportService::class,
            [
                'rateResource' => $rateResource
            ]
        );

        $rateResource->expects($this->any())->method('batchInsert')->willReturnOnConsecutiveCalls(true, true, true, false);

        $this->assertEquals(
            ['Line #3: duplicated conditions before this line have been skipped'],
            $model->returnErrors($data, 5, 3, [])
        );

        $this->assertEquals(
            ['Your csv file has been automatically cleared of duplicates and successfully uploaded'],
            $model->returnErrors($data, 5, 3, [])
        );
    }
}
