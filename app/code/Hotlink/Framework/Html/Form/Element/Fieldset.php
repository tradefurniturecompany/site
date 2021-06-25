<?php
namespace Hotlink\Framework\Html\Form\Element;

class Fieldset extends \Magento\Framework\Data\Form\Element\Fieldset
{

    public function addEntity( \Hotlink\Framework\Html\IFormHelper $object, $params = array() )
    {
        $object->getFormHelper()->addFields( $this, $object, $params );
        return $this;
    }

}