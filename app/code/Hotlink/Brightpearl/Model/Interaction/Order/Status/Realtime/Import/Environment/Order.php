<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Status\Realtime\Import\Environment;

class Order extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    public function getDefault()
    {
        return null;
    }

    public function getName()
    {
        return "Brightpearl order id";
    }

    public function getKey()
    {
        return 'order_id';
    }

    public function getNote()
    {
        return 'The ID of the Brightpearl order';
    }
}
