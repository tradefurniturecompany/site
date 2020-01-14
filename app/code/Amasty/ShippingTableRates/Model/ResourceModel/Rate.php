<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\ResourceModel;

/**
 * Rate Resource
 */
class Rate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_table_rate', 'id');
    }

    public function deleteBy($methodId)
    {
        $this->getConnection()->delete($this->getMainTable(), 'method_id=' . (int)$methodId);
    }

    /**
     * @param int $methodId shipping method
     * @param array $data importing from csv and already exploded
     * @return string
     */
    public function batchInsert($methodId, $data)
    {
        $resultArray = [];
        $err = '';
        foreach ($data as $rate) {
            array_unshift($rate, $methodId);
            $resultArray[] = $rate;
        }
        $arrayWithFields = [
            'method_id',
            'country',
            'state',
            'city',
            'zip_from',
            'zip_to',
            'price_from',
            'price_to',
            'weight_from',
            'weight_to',
            'qty_from',
            'qty_to',
            'shipping_type',
            'cost_base',
            'cost_percent',
            'cost_product',
            'cost_weight',
            'time_delivery',
            'name_delivery',
            'num_zip_from',
            'num_zip_to'
        ];

        try {
            $this->getConnection()->insertArray($this->getMainTable(), $arrayWithFields, $resultArray);
        } catch (\Exception $e) {
            $err = $e->getMessage();
        }

        return $err;
    }
}
