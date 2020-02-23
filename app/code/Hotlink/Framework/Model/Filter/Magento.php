<?php
namespace Hotlink\Framework\Model\Filter;

class Magento extends \Hotlink\Framework\Model\Filter\Base
{

    //
    //  Settings to overload in derived classes
    //
    protected $_model;
    protected $_field = 'entity_id';
    protected $_identifiers = array();
    protected $_flags = array();

    //
    //  Performance related settings
    //
    protected $_required = true;
    protected $_attributes = array( '*' );

    //
    //  Name of the Magento model to invoke
    //

    /**
     * @var \Hotlink\Framework\Model\Config\Field\Identifier\Source
     */
    protected $interactionConfigFieldIdentifierSource;

    function __construct(
        \Hotlink\Framework\Model\Config\Field\Identifier\Source $interactionConfigFieldIdentifierSource
    ) {
        $this->interactionConfigFieldIdentifierSource = $interactionConfigFieldIdentifierSource;
    }

    function getModel()
    {
        return $this->_model;
    }

    function setModel( $value )
    {
        $this->_model = $value;
        return $this;
    }

    function addFlag( $name )
    {
        $this->_flags[] = $name;
        return $this;
    }

    function getFlags()
    {
        return $this->_flags;
    }

    //
    //  Name of field that the identifier values reference
    //
    function getField()
    {
        return $this->_field;
    }

    function setField( $value )
    {
        $this->_field = $value;
        return $this;
    }

    function getFields()
    {
        return $this->interactionConfigFieldIdentifierSource->getOptions();
    }

    //
    //  Array of unique identifiers
    //
    function getIdentifiers()
    {
        return $this->_identifiers;
    }

    function setIdentifiers( $value )
    {
        if ( !is_array( $value ) )
            {
                $value = array( $value );
            }
        $this->_identifiers = $value;
        return $this;
    }

    //
    //  When true, any object not found will throw an exception. When false, missing objects are permitted.
    //
    function getRequired()
    {
        return $this->_required;
    }

    function setRequired( $value )
    {
        $this->_required = $value;
        return $this;
    }

    //
    //  an array of attributes to select, default is "*" denoting all
    //
    function getAttributes()
    {
        return $this->_attributes;
    }

    function setAttributes( array $value )
    {
        $this->_attributes = $value;
        return $this;
    }

}