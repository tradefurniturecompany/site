<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime;

class Import extends \Hotlink\Framework\Model\Interaction\AbstractInteraction
{
    protected function _getName()
    {
        return "Import stock levels (real-time)";
    }
}