<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Realtime\Import\Environment;

class Order extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    function getDefault()
    {
        return null;
    }

    function getName()
    {
        return "Brightpearl order id";
    }

    function getKey()
    {
        return 'order_id';
    }

    function getNote()
    {
        return 'The ID of the Brightpearl order';
    }
}
