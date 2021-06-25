<?php
namespace Hotlink\Framework\Html\Form\Environment\Parameter;

class Scalar extends \Hotlink\Framework\Helper\Html\Form\AbstractForm
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
        return 'scalar';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Interaction\Environment\Parameter\Scalar\AbstractScalar $parameter )
    {
        $this->_initObjectHeader( $parameter, $fieldset );

        $label = $parameter->getName();
        $value = $parameter->getValue();

        $name = $this->_getHtmlNameData( $fieldset );
        $scalar = $this->getFieldHelper()->createScalar( $fieldset, $name, $label, $value, $parameter->getOptions() );
        $scalar->setNote( $parameter->getNote() );
    }

    public function getObject( $form, $environment )
    {
        $scalar = $this->factoryHelper->create( $this->_getClass( $form ) );
        if ( $data = $this->_getData( $form ) )
            {
                $value = ( isset( $data[ 'value' ] ) ) ? $data[ 'value' ] : 0;
                $unit = ( isset( $data[ 'units' ] ) ) ? $data[ 'units' ] : '';
                $scalar->setUnit( $unit );
                $scalar->setValue( $value . ' ' . $unit );
            }
        return $scalar;
    }

}