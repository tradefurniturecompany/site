<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Reconciliation\Environment;

class Errors extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $configConfigSourceYesno;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Magento\Config\Model\Config\Source\Yesno $configConfigSourceYesno
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->configConfigSourceYesno = $configConfigSourceYesno;
    }

    public function getName()
    {
        return 'Requeue errors';
    }

    public function getKey()
    {
        return 'requeue_with_errors';
    }

    public function getNote()
    {
        return 'Select Yes to requeue orders with errors in Brightpearl.<br/> Select No otherwise.';
    }

    public function getDefault()
    {
        return 0;
    }

    public function getOptions()
    {
        return $this->configConfigSourceYesno->toArray();
    }

    public function toOptionArray()
    {
        return $this->configConfigSourceYesno->toOptionArray();
    }

    public function getMultiselect()
    {
        return false;
    }

    public function getValue()
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