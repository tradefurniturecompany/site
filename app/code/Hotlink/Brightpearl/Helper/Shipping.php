<?php
namespace Hotlink\Brightpearl\Helper;

class Shipping
{

    const SEP = "_";

    protected $exception;

    function __construct( \Hotlink\Framework\Helper\Exception $exception )
    {
        $this->exception = $exception;
    }

    function encode( $carrier, $method )
    {
        if ( !$carrier )
            {
                $this->exception->throwImplementation( "Carrier code cannot be empty", $this );
            }
        if ( strpos( $carrier, self::SEP ) !== false )
            {
                $msg = "Carrier code [" . $carrier . "] not supported (contains '" . self::SEP . "')";
                $this->exception->throwProcessing( $msg , $this );
            }

        if ( !$method )
            {
                return $carrier;
            }
        $result = implode( self::SEP, [ $carrier, $method ] );
        return $result;
    }

    function decode( $carrierMethod )
    {
        if ( !$carrierMethod )
            {
                $this->exception->throwImplementation( "Invalid carrier code (empty)" , $this );
            }
        $parts = explode( self::SEP, $carrierMethod );
        if ( count( $parts ) == 0 )
            {
                $this->exception->throwImplementation( "Failed to decode [$carrierMethod]" , $this );
            }
        $carrier = $parts[ 0 ];
        if ( !$carrier )
            {
                $this->exception->throwImplementation( "Unable to decode carrier from [$carrierMethod]" , $this );
            }

        unset( $parts[ 0 ] );
        $method = implode( self::SEP, $parts );
        return [ $carrier, $method ];
    }

    function decodeCarrier( $carrierMethod )
    {
        return $this->decode( $carrierMethod )[ 0 ];
    }

    function decodeMethod( $carrierMethod )
    {
        return $this->decode( $carrierMethod )[ 1 ];
    }

    //
    //  Covnert our encoding to a Magento encoding
    //
    function toMagento( $encoded )
    {
        $result = str_replace( self::SEP, "_", $encoded );
        return $result;
    }

}
