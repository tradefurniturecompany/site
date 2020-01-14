<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import\Environment;

class Noteid extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    public function getDefault()
    {
        return null;
    }

    public function getName()
    {
        return "Note id";
    }

    public function getKey()
    {
        return 'goodsounote_id';
    }

    public function getNote()
    {
        return 'Brightpearl note ID';
    }

}
