<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter\Scalar;

abstract class AbstractScalar extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $_unitInitialised = false;
    protected $_unit = null;

    protected $htmlParameterScalar;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Framework\Html\Form\Environment\Parameter\Scalar $htmlParameterScalar
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->htmlParameterScalar = $htmlParameterScalar;
    }

    function getDefault()
    {
        return '1 ' . $this->getDefaultUnit();
    }

    function setUnit( $unit )
    {
        if ( !array_key_exists( $unit, $this->getOptions() ) )
            {
                $this->exception()->throwValidation( "Invalid unit assignment '$unit'", $this );
            }
        $this->_unit = $unit;
        $this->_unitInitialised = true;
        return $this;
    }

    function getUnit()
    {
        if ( !$this->_unitInitialised )
            {
                $this->setUnit( $this->getDefaultUnit() );
            }
        return $this->_unit;

    }

    function getDefaultUnit()
    {
        foreach ( $this->getOptions() as $value => $label )
            {
                return $value;
            }
        return null;
    }

    function getFormHelper()
    {
        return $this->htmlParameterScalar;
    }

    function asString()
    {
        $output = $this->getName() . ' = ' . $this->getValue();
        return $output;
    }

}
