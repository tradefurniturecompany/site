<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl;

class Warehouse extends \Hotlink\Brightpearl\Model\Platform\Data
{

    public function getChildClass( $key )
    {
        return '\Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Warehouse\Address';
    }

}