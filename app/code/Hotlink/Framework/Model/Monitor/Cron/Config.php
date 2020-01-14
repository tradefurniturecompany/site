<?php
namespace Hotlink\Framework\Model\Monitor\Cron;

class Config extends \Hotlink\Framework\Model\Monitor\Config\AbstractConfig
{

    protected function _getSection()
    {
        return $this->_getInteraction()->getConfig()->getSection();
    }

    protected function _getGroup()
    {
        return $this->_getInteraction()->getConfig()->getGroup();
    }

}
