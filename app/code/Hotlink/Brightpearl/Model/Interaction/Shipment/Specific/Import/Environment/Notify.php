<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import\Environment;

class Notify extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $configConfigSourceYesno;

    function __construct(
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $interactionHtmlFormEnvironmentParameterHelper,

        \Magento\Config\Model\Config\Source\Yesno $configConfigSourceYesno
    ) {
        $this->configConfigSourceYesno = $configConfigSourceYesno;

        parent::__construct(
            $interactionExceptionHelper,
            $interactionHtmlFormEnvironmentParameterHelper );
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
        return 'Email customer a copy of shipment. Subject to sales_email/order being enabled.';
    }

   function getOptions()
    {
        return $this->configConfigSourceYesno->toArray();
    }

    function toOptionArray()
    {
        return $this->configConfigSourceYesno->toOptionArray();
    }

    function getValue()
    {
        if (!$this->_valueInitialised) {
            $storeId = $this->getEnvironment()->getStoreId();
            $this->setValue( $this->getEnvironment()->getConfig()->getNotifyCustomer($storeId) );
        }
        return $this->_value;
    }
}
