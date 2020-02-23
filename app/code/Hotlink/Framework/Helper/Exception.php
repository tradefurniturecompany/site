<?php
namespace Hotlink\Framework\Helper;

class Exception
{

    function throwCommunicationSoap( $message, $caller, \Exception $previous = null )
    {
        $message = $this->getMessageCaller( $message, $caller );
        $this->throwException( '\Hotlink\Framework\Model\Exception\Communication\Soap', $message, $previous );
    }

    function throwImplementation( $message, $caller, \Exception $previous = null )
    {
        $message = $this->getMessageCaller( $message, $caller );
        $this->throwException( '\Hotlink\Framework\Model\Exception\Implementation', $message, $previous );
    }

    function throwConfiguration( $message, $caller, \Exception $previous = null )
    {
        $message = $this->getMessageCaller( $message, $caller );
        $this->throwException( '\Hotlink\Framework\Model\Exception\Configuration', $message, $previous );
    }

    function throwProcessing( $message, $caller, \Exception $previous = null )
    {
        $message = $this->getMessageCaller( $message, $caller );
        $this->throwException( '\Hotlink\Framework\Model\Exception\Processing', $message, $previous );
    }

    function throwTransport( $message, $caller, \Exception $previous = null )
    {
        $message = $this->getMessageCaller( $message, $caller );
        $this->throwException( '\Hotlink\Framework\Model\Exception\Transport', $message, $previous );
    }

    function throwValidation( $message, $caller, \Exception $previous = null )
    {
        $final = '';
        if ( is_array( $message ) )
            {
                $combined = "";
                foreach ( $message as $key => $value )
                    {
                        if ( strlen( $combined ) > 0 )
                            {
                                $combined .= "\n";
                            }
                        $combined .= $this->getMessageCaller( $value, $caller );
                    }
                $message = $combined;
            }
        else
            {
                $message = $this->getMessageCaller( $message, $caller );
            }
        $this->throwException( '\Hotlink\Framework\Model\Exception\Validation', $message, $previous );
    }

    protected function getMessageCaller( $message, $caller )
    {
        $classname = get_class( $caller );
        $message = str_replace( "[class]", $classname, $message );
        return $message;
    }

    protected function throwException( $class, $message, $previous )
    { // TODO: rework of messaging
        throw new $class( $message, 0, $previous );
    }

}
