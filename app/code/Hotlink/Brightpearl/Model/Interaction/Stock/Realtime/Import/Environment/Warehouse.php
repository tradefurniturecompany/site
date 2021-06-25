<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Warehouse extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $brightpearlConfigSourceBrightpearlWarehouse;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Warehouse $brightpearlConfigSourceBrightpearlWarehouse
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->brightpearlConfigSourceBrightpearlWarehouse = $brightpearlConfigSourceBrightpearlWarehouse;
    }

    function getDefault()
    {
        return null;
    }

    function getKey()
    {
        return 'warehouse';
    }

    function getName()
    {
        return 'Brightpearl warehouse(s)';
    }

    function getNote()
    {
        return 'Warehouse(s) used to calculate inventory levels.';
    }

    function getOptions()
    {
        return $this->brightpearlConfigSourceBrightpearlWarehouse->toArray();
    }

    function toOptionArray()
    {
        return $this->brightpearlConfigSourceBrightpearlWarehouse->toOptionArray();
    }

    function getMultiSelect()
    {
        return true;
    }

    function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getWarehouses( $storeId ) );
            }
        return $this->_value;
    }

}