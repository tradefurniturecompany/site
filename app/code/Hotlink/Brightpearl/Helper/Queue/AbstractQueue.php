<?php
namespace Hotlink\Brightpearl\Helper\Queue;

abstract class AbstractQueue
{

    abstract function getObject( $entity );

    //
    //  Magento does not necessarily use the same object throughout a single request (eg. the payment object handled in after_save is not
    //  the same payment object handled during the order export interaction). Hence use of a cache to ensure the same tracking object
    //  throughout a single request.
    //
    protected $_cache = [];


    function update( $object, $inBP, $sendToBP, $sentAt = null )
    {
        $object
            ->setInBp( $inBP )
            ->setSendToBp( $sendToBP );

        if ( $sentAt !== null )
            {
                $object->setSentAt( $sentAt );
            }
        $object->save();
    }

    protected function _getObject( $entity, $queueFactory, $queueFactoryIdField )
    {
        $tracking = false;
        if ( $id = $entity->getId() )
            {
                $tracking = $this->_getCache( $id );
                if ( ! $tracking )
                    {
                        $tracking = $queueFactory->create();
                        $tracking->load( $id, $queueFactoryIdField );
                        $tracking->setParentId( $id );
                        $this->_setCache( $id, $tracking );
                    }
            }
        return $tracking;
    }

    protected function _setCache( $id, $object )
    {
        $this->_cache[ $id ] = $object;
    }

    protected function _getCache( $id )
    {
        return isset( $this->_cache[ $id ] )
            ? $this->_cache[ $id ]
            : false;
    }

}