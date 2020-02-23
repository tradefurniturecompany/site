<?php
namespace Hotlink\Framework\Helper;

class Html
{

    protected $reflection;

    function __construct( \Hotlink\Framework\Helper\Reflection $reflection )
    {
        $this->reflection = $reflection;
    }

    function getHtmlNameOld( $thing )
    {
        $name = is_object( $thing ) ? get_class( $thing ) : ( string ) $thing;
        $name = strtolower( $name );
        $name = str_replace( '\\', '-', $name );
        if ( substr( $name, 0, 1 ) == '-' )
            {
                $name = substr( $name, 1 );
            }
        return $name;
    }

    function getHtmlName2( $thing )
    {
        $name = is_object( $thing ) ? get_class( $thing ) : ( string ) $thing;
        if ( substr( $name, 0, 1 ) !== '\\' )
            {
                $name = '\\' . $name;
            }
        return $name;
    }

    function getHtmlName( $thing )
    {
        $name = 
        $name = is_object( $thing ) ? get_class( $thing ) : ( string ) $thing;
        $name = str_replace( '\\', '-', $name );
        if ( substr( $name, 0, 1 ) == '-' )
            {
                $name = substr( $name, 1 );
            }
        return $name;
    }

    function encode( $thing )
    {
        $name = $this->reflection->getClass( $thing );
        $name = str_replace( '\\', '-', $name );
        if ( substr( $name, 0, 1 ) == '-' )
            {
                $name = substr( $name, 1 );
            }
        return $name;
    }

    function decode( $thing )
    {
        $name = is_object( $thing ) ? get_class( $thing ) : ( string ) $thing;
        $name = $this->reflection->getClass( $thing );
        $name = str_replace( '-', '\\', $name );
        if ( substr( $name, 0, 1 ) != '\\' )
            {
                $name = '\\' . $name;
            }
        return $name;
    }

}
