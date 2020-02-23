<?php
namespace Hotlink\Framework\Helper\Html\Form\Environment;

class Parameter extends \Hotlink\Framework\Helper\Html\Form\AbstractForm
{

    protected $factory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $fieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper,
        \Hotlink\Framework\Helper\Factory $factory
    )
    {
        $this->factory = $factory;
        parent::__construct( $interactionExceptionHelper,
                             $fieldsetHelper,
                             $htmlHelper );
    }

    function getHtmlKey()
    {
        return 'parameter';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter $parameter )
    {
        $key = $parameter->getKey();
        $this->_initObjectHeader( $parameter, $fieldset, $key );

        $label = $parameter->getName();
        $value = $parameter->getValue();
        $options = $parameter->getOptions();
        $note = $parameter->getNote();

        $htmlName = $this->_getHtmlNameData( $fieldset, $key ) . '[value]';

        if ( $this->isTextField($parameter) )
            {
                $this->getFieldHelper()
                    ->createText( $fieldset, $htmlName, $label, $value )
                    ->setNote( $note );
            }
        elseif ($this->isMultiselect($parameter))
            {
                $options = $parameter->toOptionArray();
                $field = $this->getFieldHelper()
                    ->createMultiSelect( $fieldset, $htmlName, $label, $value, $options )
                    ->setNote( $note );

                if ( $size = $parameter->getSize() )
                    {
                        $field->setSize( $size );
                    }
                else
                    {
                        $size = count( $options ) + 1;
                        $field->setSize( $size );
                    }

            }
        elseif ($this->isSelect($parameter))
            {
                $this->getFieldHelper()
                    ->createSelect( $fieldset, $htmlName, $label, $value, $parameter->toOptionArray() )
                    ->setNote( $note );
            }
        else
            {
                $this->getFieldHelper()
                    ->createText( $fieldset, $htmlName, $label, $this->arrayToCsv( $value ) )
                    ->setNote( $note );
            }
    }

    function getObject( $form, $environment )
    {
        $parameter = $this->factory->create( $this->_getClass( $form ) );
        if ( $data = $this->_getData( $form ) )
            {
                if ( array_key_exists( 'value', $data ) )
                    {
                        $parameter->setValue( $data[ 'value' ] );
                    }
            }
        else
            {
                if ($this->isTextField($parameter) || $this->isSelect($parameter)) {
                    $parameter->setValue( null );
                }
                else if ($this->isMultiselect($parameter)) {
                    $parameter->setValue( array() );
                }
                else {
                    $parameter->setValue( '' );
                }
            }
        return $parameter;
    }

    protected function isTextField($parameter)
    {
        return $parameter->getOptions() == false;
    }

    protected function isMultiselect($parameter)
    {
        return count($parameter->getOptions()) > 0 && $parameter->getMultiselect();
    }

    protected function isSelect($parameter)
    {
        return count($parameter->getOptions()) > 0 && !$parameter->getMultiselect();
    }

}
