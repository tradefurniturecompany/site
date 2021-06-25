<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import\Environment;

class Noteid extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    function getDefault()
    {
        return null;
    }

    function getName()
    {
        return "Note id";
    }

    function getKey()
    {
        return 'goodsoutnote_id';
    }

    function getNote()
    {
        return 'Brightpearl note ID';
    }

}
