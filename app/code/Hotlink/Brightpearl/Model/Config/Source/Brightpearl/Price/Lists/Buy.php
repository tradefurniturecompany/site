<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists;

class Buy extends \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists
{

    protected function getCollection()
    {
        $collection = $this->priceListItemCollectionFactory->create();
        $collection->addFilter( 'price_list_type_code', 'BUY' );
        return $collection;
    }

}