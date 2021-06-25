<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Lookup\Warehouse', '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse' );
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray( 'id', 'name' );
    }

    public function toOptionHash()
    {
        return $this->toOptionHash( 'id', 'name' );
    }

    public function addIdFilter( $warehouseId, $exclude = false )
    {
        if ( !is_array( $warehouseId ) )
            {
                $warehouseId = [ $warehouseId ];
            }

        if ( $exclude )
            {
                $condition = [ 'nin' => $warehouseId ];
            }
        else
            {
                $condition = [ 'in' => $warehouseId ];
            }
        $this->addFieldToFilter( 'id', $condition );
        return $this;
    }

    public function addActiveFilter()
    {
        $this->addFieldToFilter( 'deleted', [ 'eq' => 0 ] );
        return $this;
    }

}