<?php
namespace Hotlink\Framework\Html;

class Form extends \Magento\Framework\Data\Form
{

    /*
      This call utilises a custom fieldset that permits adding objects (which define reusable field structures) to a form
     */
    public function addFieldset( $elementId, $config, $after = false, $isAdvanced = false )
    {
        $element = $this->_factoryElement->create( '\Hotlink\Framework\Html\Form\Element\Fieldset', [ 'data' => $config ] );
        $element->setId( $elementId );
        $this->addElement($element, $after);
        return $element;
    }

}
