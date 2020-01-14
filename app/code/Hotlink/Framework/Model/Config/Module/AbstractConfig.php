<?php
namespace Hotlink\Framework\Model\Config\Module;

abstract class AbstractConfig extends \Hotlink\Framework\Model\Config\AbstractConfig
{

    protected function _getSection()
    {
        return 'hotlink_framework';
    }

}
