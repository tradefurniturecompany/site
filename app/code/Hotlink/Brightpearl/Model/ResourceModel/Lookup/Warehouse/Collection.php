<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';

    function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\Lookup\Warehouse', '\Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse' );
    }

    function toOptionArray()
    {
        return $this->_toOptionArray( 'id', 'name' );
    }

    function toOptionHash()
    {
        return $this->toOptionHash( 'id', 'name' );
    }

    function addIdFilter( $warehouseId, $exclude = false )
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

    function addActiveFilter()
    {
        $this->addFieldToFilter( 'deleted', [ 'eq' => 0 ] );
        return $this;
    }

}