<?php
namespace Hotlink\Brightpearl\Model;

class Shipment extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init( '\Hotlink\Brightpearl\Model\ResourceModel\Shipment', 'id' );
    }

    public function loadWithType( $noteId, $noteType )
    {
        $this->getResource()->loadWithType($this, $noteId, $noteType);
        return $this;
    }
}