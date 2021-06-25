<?php
namespace Hotlink\Framework\Html\Form\Environment\Parameter;

class Boolean extends \Hotlink\Framework\Helper\Html\Form\AbstractForm
{

    protected $factoryHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $fieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper,

        \Hotlink\Framework\Helper\Factory $factoryHelper
    )
    {
        parent::__construct( $exceptionHelper, $fieldsetHelper, $htmlHelper );
        $this->factoryHelper = $factoryHelper;
    }

    public function getHtmlKey()
    {
        return 'parameter_boolean';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean $boolean )
    {
        $key = $boolean->getKey();
        $this->_initObjectHeader( $boolean, $fieldset, $key );

        $label = ( $boolean->getName() ) ? $boolean->getName() : 'Tick for yes';
        $note = ( $boolean->getNote() ) ? $boolean->getNote() : '';

        $htmlName = $this->_getHtmlNameData( $fieldset, $key ) . '[value]';
        $element = $this->getFieldHelper()->createCheckbox( $fieldset, $htmlName, $label, $boolean->getValue() );
        $element->setNote( $note );
    }

    public function getObject( $form, $environment )
    {
        $boolean = $this->factoryHelper->create( $this->_getClass( $form ) );
        $value = ( $this->_getData( $form ) ) ? true : false;
        $boolean->setValue( $value );
        return $boolean;
    }

}
