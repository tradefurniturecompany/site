<?php
namespace Hotlink\Brightpearl\Model\ResourceModel\Queue;

class Payment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function _construct()
    {
        $this->_init( 'hotlink_brightpearl_queue_payment', 'id' );
    }

    protected function _prepareDataForSave( \Magento\Framework\Model\AbstractModel $object )
    {
        if ( ( !$object->getId() || $object->isObjectNew() ) && !$object->getCreatedAt() )
            {
                $object->setCreatedAt( gmdate( \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT ) );
            }
        return parent::_prepareDataForSave($object);
    }

}