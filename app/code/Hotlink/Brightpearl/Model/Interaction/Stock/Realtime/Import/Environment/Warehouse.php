<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Warehouse extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $brightpearlConfigSourceBrightpearlWarehouse;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Warehouse $brightpearlConfigSourceBrightpearlWarehouse
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->brightpearlConfigSourceBrightpearlWarehouse = $brightpearlConfigSourceBrightpearlWarehouse;
    }

    public function getDefault()
    {
        return null;
    }

    public function getKey()
    {
        return 'warehouse';
    }

    public function getName()
    {
        return 'Brightpearl warehouse(s)';
    }

    public function getNote()
    {
        return 'Warehouse(s) used to calculate inventory levels.';
    }

    public function getOptions()
    {
        return $this->brightpearlConfigSourceBrightpearlWarehouse->toArray();
    }

    public function toOptionArray()
    {
        return $this->brightpearlConfigSourceBrightpearlWarehouse->toOptionArray();
    }

    public function getMultiSelect()
    {
        return true;
    }

    public function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $storeId = $this->getEnvironment()->getStoreId();
                $this->setValue( $this->getEnvironment()->getConfig()->getWarehouses( $storeId ) );
            }
        return $this->_value;
    }

}