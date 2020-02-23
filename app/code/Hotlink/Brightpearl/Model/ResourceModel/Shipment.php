<?php
namespace Hotlink\Brightpearl\Model\ResourceModel;

class Shipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    function _construct()
    {
        $this->_init( 'hotlink_brightpearl_shipment', 'id' );
    }

    function loadWithType( $object, $noteId, $noteType )
    {
        $read = $this->getConnection();
        if ( $read ) {
            $select = $this->_getLoadSelect('brightpearl_id', $noteId, $object);
            $field  = $this->getConnection()->quoteIdentifier( sprintf('%s.%s', $this->getMainTable(), 'shipment_type') );
            $select->where($field . '=?', $noteType);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        return $this;
    }
}
