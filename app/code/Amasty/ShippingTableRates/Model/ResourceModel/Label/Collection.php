<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\ResourceModel\Label;

/**
 * Method Label Resource Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\ShippingTableRates\Model\Label::class,
            \Amasty\ShippingTableRates\Model\ResourceModel\Label::class
        );
    }

    public function addFiltersByMethodIdStoreId($methodId, $storeId)
    {
        $this->getSelect()->reset('where');
        $this->clear()
            ->addFieldToFilter('method_id', $methodId)
            ->addFieldToFilter('store_id', $storeId);

        return $this;
    }
}
