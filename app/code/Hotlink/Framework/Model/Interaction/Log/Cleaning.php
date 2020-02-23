<?php
namespace Hotlink\Framework\Model\Interaction\Log;

class Cleaning extends \Hotlink\Framework\Model\Interaction\AbstractInteraction implements \Hotlink\Framework\Html\IFormHelper
{

    protected function _getName()
    {
        return "Log Cleaning";
    }

    function getTabBlock()
    {
        return '\Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab\DefaultTab';
    }

}
