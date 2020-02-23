<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Errors extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $configConfigSourceYesno;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Magento\Config\Model\Config\Source\Yesno $configConfigSourceYesno
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->configConfigSourceYesno = $configConfigSourceYesno;
    }

    function getName()
    {
        return 'Requeue errors';
    }

    function getKey()
    {
        return 'requeue_with_errors';
    }

    function getNote()
    {
        return 'Select Yes to requeue orders with errors in Brightpearl.<br/> Select No otherwise.';
    }

    function getDefault()
    {
        return 0;
    }

    function getOptions()
    {
        return $this->configConfigSourceYesno->toArray();
    }

    function toOptionArray()
    {
        return $this->configConfigSourceYesno->toOptionArray();
    }

    function getMultiselect()
    {
        return false;
    }

    function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $env = $this->getEnvironment();
                $storeId = $env->getStoreId();
                $this->setValue( $env->getConfig()->getConfigData( $this->getKey(), $storeId, $this->getDefault()) );
            }
        return $this->_value;
    }
}