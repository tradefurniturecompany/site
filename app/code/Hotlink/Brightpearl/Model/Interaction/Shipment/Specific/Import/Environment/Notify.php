<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import\Environment;

class Notify extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $configConfigSourceYesno;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $interactionHtmlFormEnvironmentParameterHelper,

        \Magento\Config\Model\Config\Source\Yesno $configConfigSourceYesno
    ) {
        $this->configConfigSourceYesno = $configConfigSourceYesno;

        parent::__construct(
            $interactionExceptionHelper,
            $interactionHtmlFormEnvironmentParameterHelper );
    }

    public function getDefault()
    {
        return 1;
    }

    public function getName()
    {
        return "Notify customer";
    }

    public function getKey()
    {
        return 'notify_customer';
    }

    public function getNote()
    {
        return 'Email customer a copy of shipment. Subject to sales_email/order being enabled.';
    }

   public function getOptions()
    {
        return $this->configConfigSourceYesno->toArray();
    }

    public function toOptionArray()
    {
        return $this->configConfigSourceYesno->toOptionArray();
    }

    public function getValue()
    {
        if (!$this->_valueInitialised) {
            $storeId = $this->getEnvironment()->getStoreId();
            $this->setValue( $this->getEnvironment()->getConfig()->getNotifyCustomer($storeId) );
        }
        return $this->_value;
    }
}
