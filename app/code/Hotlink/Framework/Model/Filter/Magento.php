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

    public function __construct(
        \Hotlink\Framework\Model\Config\Field\Identifier\Source $interactionConfigFieldIdentifierSource
    ) {
        $this->interactionConfigFieldIdentifierSource = $interactionConfigFieldIdentifierSource;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function setModel( $value )
    {
        $this->_model = $value;
        return $this;
    }

    public function addFlag( $name )
    {
        $this->_flags[] = $name;
        return $this;
    }

    public function getFlags()
    {
        return $this->_flags;
    }

    //
    //  Name of field that the identifier values reference
    //
    public function getField()
    {
        return $this->_field;
    }

    public function setField( $value )
    {
        $this->_field = $value;
        return $this;
    }

    public function getFields()
    {
        return $this->interactionConfigFieldIdentifierSource->getOptions();
    }

    //
    //  Array of unique identifiers
    //
    public function getIdentifiers()
    {
        return $this->_identifiers;
    }

    public function setIdentifiers( $value )
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
    public function getRequired()
    {
        return $this->_required;
    }

    public function setRequired( $value )
    {
        $this->_required = $value;
        return $this;
    }

    //
    //  an array of attributes to select, default is "*" denoting all
    //
    public function getAttributes()
    {
        return $this->_attributes;
    }

    public function setAttributes( array $value )
    {
        $this->_attributes = $value;
        return $this;
    }

}