<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import\Environment;

class Notetype extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $shipmentTypeSource;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type $shipmentTypeSource
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->shipmentTypeSource = $shipmentTypeSource;
    }

    function getDefault()
    {
        return 'goods_out';
    }

    function getKey()
    {
        return 'note_type';
    }

    function getName()
    {
        return 'Note type';
    }

    function getNote()
    {
        return "Choose from 'goods_out' or 'drop_ship'";
    }

    function getOptions()
    {
        return $this->shipmentTypeSource->toArray();
    }

    function toOptionArray()
    {
        return $this->shipmentTypeSource->toOptionArray();
    }

}
