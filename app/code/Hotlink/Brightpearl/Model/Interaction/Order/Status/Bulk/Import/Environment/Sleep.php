<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Bulk\Import\Environment;

class Sleep extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{
    public function getDefault()
    {
        return 50000;
    }

    public function getName()
    {
        return 'Sleep between saves';
    }

    public function getNote()
    {
        return 'Millionths of a second (micro seconds)';
    }

    public function getKey()
    {
        return 'sleep';
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
