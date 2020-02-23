<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Realtime\Import\Environment;

class NotifyCustomer extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
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

    function getDefault()
    {
        return 1;
    }

    function getName()
    {
        return "Notify customer";
    }

    function getKey()
    {
        return 'notify_customer';
    }

    function getNote()
    {
        return 'Send customer an email about order status change';
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
        if ( !$this->_valueInitialised ) {
            $storeId = $this->getEnvironment()->getStoreId();
            $this->setValue( $this->getEnvironment()->getConfig()->getNotifyCustomer($storeId) );
        }
        return $this->_value;
    }
}
