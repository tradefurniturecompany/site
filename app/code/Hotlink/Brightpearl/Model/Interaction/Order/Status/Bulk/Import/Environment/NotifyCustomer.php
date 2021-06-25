<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import\Environment;

class NotifyCustomer extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
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
        return 'Send customer an email about order status change';
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
        if (!$this->_valueInitialised) {
            $storeId = $this->getEnvironment()->getStoreId();
            $this->setValue($this->getEnvironment()->getConfig()->getConfigData($this->getKey(), $storeId, $this->getDefault()));
        }
        return $this->_value;
    }
}
