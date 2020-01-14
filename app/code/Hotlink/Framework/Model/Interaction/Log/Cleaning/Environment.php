<?php
namespace Hotlink\Framework\Model\Interaction\Log\Cleaning;

class Environment extends \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment
{

    protected function _getParameterModels()
    {
        return [ '\Hotlink\Framework\Model\Interaction\Log\Cleaning\Environment\Count',
                 '\Hotlink\Framework\Model\Interaction\Log\Cleaning\Environment\Sleep' ];
    }

}