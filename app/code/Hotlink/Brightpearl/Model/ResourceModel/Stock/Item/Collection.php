<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Stock\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init( 'Hotlink\Brightpearl\Model\Stock\Item', 'Hotlink\Brightpearl\Model\ResourceModel\Stock\Item' );
    }

    public function addStockItemFilter( $items )
    {
        $itemIds = array();
        foreach ( $items as $item )
            {
                if ( $item instanceof \Magento\CatalogInventory\Model\Stock\Item )
                    {
                        $id = $item->getId();
                        $itemIds[ $id ] = $id;
                    }
                else
                    {
                        $itemIds[ $item ] = $item;
                    }
            }
        if ( empty( $itemIds ) )
            {
                $this->_setIsLoaded(true);
            }
        $this->addFieldToFilter( 'main_table.item_id', [ 'in' => $itemIds ] );
        return $this;
    }

}