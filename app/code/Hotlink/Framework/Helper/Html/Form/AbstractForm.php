<?php
namespace Hotlink\Framework\Helper\Html\Form;

abstract class AbstractForm
{

    const HTML_KEY = 'undefined';
    const HTML_KEY_CLASS = 'class';
    const HTML_KEY_DATA = 'data';

    // protected function _addFields is left undeclared so that parameter type enforcement can be implemented.

    protected $interactionExceptionHelper;
    protected $fieldsetHelper;
    protected $htmlHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $fieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper
    ) {
        $this->interactionExceptionHelper = $interactionExceptionHelper;
        $this->fieldsetHelper = $fieldsetHelper;
        $this->htmlHelper = $htmlHelper;
    }

    abstract function getObject( $data, $parent );
    abstract function getHtmlKey();

    protected function _getHtmlHelper()
    {
        return $this->htmlHelper;
    }

    protected function _encode( $thing )
    {
        return $this->_getHtmlHelper()->encode( $thing );
    }

    protected function _decode( $thing )
    {
        return $this->_getHtmlHelper()->decode( $thing );
    }

    //protected function _initObjectHeader( \Hotlink\Framework\Model\AbstractModel $object, $fieldset, $namesuffix = '' )
    protected function _initObjectHeader( $object, $fieldset, $namesuffix = '' )
    {
        $this->getFieldHelper()->createHidden( $fieldset, $this->_getHtmlNameBase( $fieldset, $namesuffix ), '' );
        $className = $this->_encode( $object );
        $this->getFieldHelper()->createHidden( $fieldset, $this->_getHtmlNameModel( $fieldset, $namesuffix ), $className );
    }

    protected function _getHtmlNameBase( $fieldset, $namesuffix = '' )
    {
        $helper = $this->_encode( $this );
        $namesuffix = ( $namesuffix ) ? '|' . $namesuffix : '';
        return $fieldset->getName() . '[' . $helper . $namesuffix . ']' . '[' . $this->getHtmlKey() . ']';
    }

    protected function _getHtmlNameModel( $fieldset, $namesuffix = '' )
    {
        return $this->_getHtmlNameBase( $fieldset, $namesuffix ) . '[' . self::HTML_KEY_CLASS . ']';
    }

    protected function _getHtmlNameData( $fieldset, $namesuffix = '' )
    {
        return $this->_getHtmlNameBase( $fieldset, $namesuffix ) . '[' . self::HTML_KEY_DATA . ']';
    }

    protected function _validate( $form, $part, $complain )
    {
        $key = $this->getHtmlKey();
        if ( !isset( $form[ $key ] ) )
            {
                $this->interactionExceptionHelper->throwProcessing( 'Invalid form data submitted in [class], unable to find key [' . $key . ']', $this );
            }
        if ( $complain && !isset( $form[ $key ][ $part ] ) )
            {
                $this->interactionExceptionHelper->throwProcessing( 'Form ' . $part . ' incorrectly submitted for deserialization in [class]', $this );
            }
        return isset( $form[ $key ][ $part ] ) ? $form[ $key ][ $part ] : false;
    }

    protected function _getClass( $form, $complain = true )
    {
        return $this->_decode( $this->_validate( $form, self::HTML_KEY_CLASS, $complain ) );
    }

    protected function _getData( $form, $complain = false )
    {
        return $this->_validate( $form, self::HTML_KEY_DATA, $complain );
    }

    //
    //  This declaration is omitted to support strong typing of $object
    //
    //  abstract protected function _addFields( $fieldset, $object );
    //
    public function addFields( $fieldset, $object )
    {
        return $this->_addFields( $fieldset, $object );
    }

    protected function getFieldHelper()
    {
        return $this->fieldsetHelper;
    }

    protected function arrayToOptionArray( $array )
    {
        // Check if we're already dealing with an option array
        reset( $array );
        $item = current( $array );
        if ( is_array( $item ) && array_key_exists( 'label', $item ) && array_key_exists( 'value', $item ) )
            {
                return $array;
            }
        $result = array();
        foreach ( $array as $value => $label )
            {
                $result[] = array( 'label' => $label, 'value' => $value );
            }
        return $result;
    }

    protected function arrayToCsv( $array )
    {
        return implode( ',', $array );
    }

    protected function csvToArray( $string )
    {
        $result = array();
        $temp = explode( ',', $string );
        foreach ( $temp as $item )
            {
                $result[] = trim( $item );
            }
        return $result;
    }

}
