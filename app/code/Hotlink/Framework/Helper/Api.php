<?php
namespace Hotlink\Framework\Helper;

class Api
{

    const TRANSPORT_TYPE_SOAP = 'soap';
    const TRANSPORT_TYPE_FILE = 'file';

    protected $_storeTransports = array();

    protected function _getTransport($storeId, $type)
    {
        if ($this->_transportExists($storeId, $type)) {
            return $this->_storeTransports[$storeId][$type];
        }

        return $this->_createTransport($storeId);
    }

    protected function _transportExists($storeId, $type)
    {
        return (array_key_exists($storeId, $this->_storeTransports) && array_key_exists($type, $this->_storeTransports[$storeId]));
    }

    protected function getTransportConfig()
    {
        return null;
    }

    protected function _createTransport($storeId)
    {
        return null;
    }

}