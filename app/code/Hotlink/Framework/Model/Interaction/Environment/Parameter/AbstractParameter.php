<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter;

abstract class AbstractParameter extends \Magento\Framework\DataObject implements \Hotlink\Framework\Html\IFormHelper
{

    abstract function getDefault();                         // Returns the default value for a parameter if none has been set
    abstract function getName();                            // Returns the name of the runtime option (used as a label)
    abstract function getKey();                             // Returns the key of the runtime option (used as a unique identifier)

    protected $exceptionHelper;
    protected $parameterHelper;

    protected $_environment = null;
    protected $_valueInitialised = false;
    protected $_value = null;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,
        array $data = []
    )
    {
        $this->exceptionHelper = $exceptionHelper;
        $this->parameterHelper = $parameterHelper;
        parent::__construct( $data );
    }

    function exception()
    {
        return $this->exceptionHelper;
    }

    function setEnvironment( \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment $environment )
    {
        if ( !is_null( $this->_environment ) )
            {
                $this->exception()->throwProcessing( 'Environment cannot be changed in [class]', $this );
            }
        $this->_environment = $environment;
        return $this;
    }

    function hasEnvironment()
    {
        return ( !is_null( $this->_environment ) );
    }

    function getEnvironment()
    {
        if ( is_null( $this->_environment ) )
            {
                $this->exception()->throwProcessing( 'Environment has not been set in [class]', $this );
            }
        return $this->_environment;
    }

    //
    //  Overload to specialise parameter values
    //
    function getValue()
    {
        if ( !$this->_valueInitialised )
            {
                $this->setValue( $this->getDefault() );
            }
        return $this->_value;
    }

    function setValue( $value )
    {
        $this->_value = $value;
        $this->_valueInitialised = true;
        return $this;
    }

    //
    //  Overload to return an array of key/values if parameter has and support options
    //
    function getOptions()
    {
        return [];
    }

    function toOptionArray()
    {
        $result = array();
        if ( $options = $this->getOptions() )
            {
                foreach ( $options as $value => $label )
                    {
                        $result[] = array( 'label' => $label, 'value' => $value );
                    }
            }
        return $result;
    }

    function getNote()
    {
        return '';
    }

    function getMultiSelect()
    {
        return false;
    }

    //
    //  IFormHelper
    //
    function getFormHelper()
    {
        //return Mage::helper( 'hotlink_framework/html_form_environment_parameter' );
        return $this->parameterHelper;
    }

    function asString()
    {
        $output = "";

        $value = $this->getValue();

        if ( !is_array( $value ) )
            {
                $value = array( $value );
            }

        $counter = 0;
        foreach ( $value as $val )
            {
                $counter++;
                if ( $counter > 1 )
                    {
                        $output .= ", ";
                    }
                $output .= $val;
                if ( $label = $this->_getLabel( $val ) )
                    {
                        $output .= " ($label)";
                    }
            }

        if ( ( count( $value ) > 1 ) || $this->getMultiSelect() )
            {
                $output = "[ " . $output . " ]";
            }

        $output = $this->getName() . ' = ' . $output;
        return $output;
    }

    protected function _getLabel( $value )
    {
        if ( $value )
            {
                if ( $options = $this->getOptions() )
                    {
                        if ( array_key_exists( $value, $options ) )
                            {
                                return $options[ $value ];
                            }
                        else
                            {
                                return '(no label defined)';
                            }
                    }
            }
        return false;
    }

    //
    // IReport
    //
    function getReportSection()
    {
        return 'parameter';
    }

}
